<?php

namespace App\Services\InclusiveRadar;

use App\Exceptions\InclusiveRadar\CannotChangeStatusWithActiveLoansException;
use App\Exceptions\InclusiveRadar\CannotDeleteWithActiveLoansException;
use App\Models\AuditLog;
use App\Models\InclusiveRadar\AccessibleEducationalMaterial;
use App\Enums\InclusiveRadar\ResourceStatus;
use Illuminate\Support\Facades\DB;
use DomainException;
use InvalidArgumentException;

class AccessibleEducationalMaterialService
{
    public function __construct(
        protected InspectionService $inspectionService,
        protected LoanService $loanService,
    ) {}

    public function store(array $data): AccessibleEducationalMaterial
    {
        return DB::transaction(
            fn() => $this->persist(new AccessibleEducationalMaterial(), $data)
        );
    }

    public function update(AccessibleEducationalMaterial $material, array $data): AccessibleEducationalMaterial
    {
        return DB::transaction(
            fn() => $this->persist($material, $data)
        );
    }

    public function delete(AccessibleEducationalMaterial $material): void
    {
        DB::transaction(function () use ($material) {
            /* Impedimos a remoção para evitar a perda do histórico de movimentação
               de itens que ainda estão sob posse de terceiros. */
            if ($material->loans()->whereNull('return_date')->exists()) {
                throw new CannotDeleteWithActiveLoansException();
            }
            $material->delete();
        });
    }

    protected function persist(AccessibleEducationalMaterial $material, array $data): AccessibleEducationalMaterial
    {
        $this->validateBusinessRules($material, $data);

        [$oldDef, $oldFeatures, $oldTrainings] = $this->captureOriginalState($material);

        $data = $this->processStock($material, $data);

        $this->validateStatusChangeWithActiveLoans($material, $data);

        $this->saveModel($material, $data);

        $this->syncRelations($material, $data);

        $this->logRelationChanges($material, $data, $oldDef, $oldFeatures, $oldTrainings);

        $this->runInspection($material, $data);

        return $this->loadFreshRelations($material);
    }

    private function validateBusinessRules(AccessibleEducationalMaterial $material, array $data): void
    {
        $isDigital = $data['is_digital'] ?? $material->is_digital ?? false;
        $isLoanable = $data['is_loanable'] ?? $material->is_loanable ?? false;

        $quantity = isset($data['quantity'])
            ? (int) $data['quantity']
            : $material->quantity;

        $available = isset($data['quantity_available'])
            ? (int) $data['quantity_available']
            : $material->quantity_available;

        if (isset($data['deficiencies']) && empty($data['deficiencies'])) {
            throw new InvalidArgumentException(
                "Selecione pelo menos um público-alvo."
            );
        }

        if (!$isDigital && $quantity <= 0) {
            throw new DomainException(
                "Para materiais físicos, a quantidade deve ser no mínimo 1."
            );
        }

        if ($isLoanable && $quantity <= 0) {
            throw new DomainException(
                "Materiais marcados como emprestáveis devem ter quantidade maior que zero."
            );
        }

        if ($available > $quantity) {
            throw new DomainException(
                "A quantidade disponível ({$available}) não pode ser maior que a quantidade total ({$quantity})."
            );
        }
    }

    private function captureOriginalState(AccessibleEducationalMaterial $material): array
    {
        $oldDeficiencies = $material->exists
            ? $material->deficiencies()->pluck('deficiencies.id')->toArray()
            : [];

        $oldFeatures = $material->exists
            ? $material->accessibilityFeatures()->pluck('accessibility_features.id')->toArray()
            : [];

        $oldTrainings = $material->exists
            ? $material->trainings()->pluck('id')->toArray()
            : [];

        return [$oldDeficiencies, $oldFeatures, $oldTrainings];
    }

    private function processStock(AccessibleEducationalMaterial $material, array $data): array
    {
        if (isset($data['quantity'])) {
            $this->loanService->validateStockAvailability($material, (int) $data['quantity']);
        }

        return $this->loanService->calculateStockForLoan($material, $data);
    }

    private function saveModel(AccessibleEducationalMaterial $material, array $data): void
    {
        if (!$material->exists && empty($data['status'])) {
            $data['status'] = ResourceStatus::AVAILABLE->value;
        }

        $material->fill($data)->save();
    }

    protected function syncRelations(AccessibleEducationalMaterial $material, array $data): void
    {
        if (isset($data['deficiencies'])) {
            $material->deficiencies()->sync($data['deficiencies']);
        }

        if (isset($data['accessibility_features'])) {
            $material->accessibilityFeatures()->sync($data['accessibility_features']);
        }

        if ($material->exists && isset($data['trainings'])) {
            /* Deletamos e recriamos treinamentos para evitar lógica complexa de comparação
               de arquivos e metadados em atualizações parciais. */
            $material->trainings()->delete();

            foreach ($data['trainings'] as $training) {
                $t = $material->trainings()->create([
                    'title'       => $training['title'],
                    'description' => $training['description'] ?? null,
                    'url'         => $training['url'] ?? null,
                    'is_active'   => true
                ]);

                if (!empty($training['files'])) {
                    foreach ($training['files'] as $file) {
                        $path = $file->store('trainings', 'public');

                        $t->files()->create([
                            'path'          => $path,
                            'original_name' => $file->getClientOriginalName(),
                            'mime_type'     => $file->getMimeType(),
                            'size'          => $file->getSize(),
                        ]);
                    }
                }
            }
        }
    }

    private function validateStatusChangeWithActiveLoans(AccessibleEducationalMaterial $material, array $data): void
    {
        if (!$material->exists || !isset($data['status'])) return;

        $hasActiveLoans = $material->loans()->whereNull('return_date')->exists();

        /* Mudanças de status (ex: Inativo ou Manutenção) são bloqueadas se houver
           empréstimos ativos para não gerar inconsistência no fluxo de devolução. */
        if ($hasActiveLoans && $material->status->value != $data['status']) {
            throw new CannotChangeStatusWithActiveLoansException();
        }
    }

    private function runInspection(AccessibleEducationalMaterial $material, array $data): void
    {
        $this->inspectionService->createInspectionForModel($material, $data);
    }

    private function loadFreshRelations(AccessibleEducationalMaterial $material): AccessibleEducationalMaterial
    {
        return $material->fresh(['deficiencies', 'accessibilityFeatures', 'trainings.files']);
    }

    private function logRelationChanges(AccessibleEducationalMaterial $material, array $data, array $oldDef, array $oldFeatures, array $oldTrainings): void
    {
        if ($material->wasRecentlyCreated) return;

        if (isset($data['deficiencies'])) {
            $this->auditIfChanged($material, 'deficiencies', $oldDef, $data['deficiencies']);
        }

        if (isset($data['accessibility_features'])) {
            $this->auditIfChanged($material, 'accessibility_features', $oldFeatures, $data['accessibility_features']);
        }

        if (isset($data['trainings'])) {
            $newTrain = $material->trainings()->pluck('id')->toArray();

            /* Ordenamos para garantir que a comparação de arrays identifique apenas mudanças
               de valores reais, ignorando se a ordem dos IDs veio diferente da requisição. */
            sort($oldTrainings);
            sort($newTrain);

            if ($oldTrainings !== $newTrain) {
                $this->logRelationChange($material, 'trainings', $oldTrainings, $newTrain);
            }
        }
    }

    protected function auditIfChanged($model, string $field, array $old, ?array $new): void
    {
        if ($new === null) return;

        $new = array_map('intval', $new);

        sort($old);
        sort($new);

        if ($old !== $new) {
            $this->logRelationChange($model, $field, $old, $new);
        }
    }

    protected function logRelationChange($model, string $field, array $old, array $new): void
    {
        AuditLog::create([
            'user_id'        => auth()->id(),
            'action'         => 'updated',
            'auditable_type' => $model->getMorphClass(),
            'auditable_id'   => $model->id,
            'old_values'     => [$field => $old],
            'new_values'     => [$field => $new],
            'ip_address'     => request()?->ip(),
            'user_agent'     => request()?->userAgent(),
        ]);
    }
}
