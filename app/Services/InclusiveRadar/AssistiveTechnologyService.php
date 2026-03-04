<?php

namespace App\Services\InclusiveRadar;

use App\Exceptions\InclusiveRadar\CannotChangeStatusWithActiveLoansException;
use App\Exceptions\InclusiveRadar\CannotDeleteWithActiveLoansException;
use App\Models\AuditLog;
use App\Models\InclusiveRadar\AssistiveTechnology;
use App\Enums\InclusiveRadar\ResourceStatus;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use DomainException;

class AssistiveTechnologyService
{
    public function __construct(
        protected InspectionService $inspectionService,
        protected LoanService $loanService,
    ) {}

    public function store(array $data): AssistiveTechnology
    {
        return DB::transaction(
            fn() => $this->persist(new AssistiveTechnology(), $data)
        );
    }

    public function update(AssistiveTechnology $assistiveTechnology, array $data): AssistiveTechnology
    {
        return DB::transaction(
            fn() => $this->persist($assistiveTechnology, $data)
        );
    }

    public function delete(AssistiveTechnology $assistiveTechnology): void
    {
        DB::transaction(function () use ($assistiveTechnology) {
            /* Impedimos a exclusão para manter a integridade histórica dos
               empréstimos ativos e evitar órfãos no sistema de rastreio. */
            if ($assistiveTechnology->loans()->whereNull('return_date')->exists()) {
                throw new CannotDeleteWithActiveLoansException();
            }
            $assistiveTechnology->delete();
        });
    }

    protected function persist(AssistiveTechnology $at, array $data): AssistiveTechnology
    {
        $this->validateBusinessRules($at, $data);

        [$oldDef, $oldTrainings] = $this->captureOriginalState($at);

        $data = $this->processStock($at, $data);

        $this->validateStatusChangeWithActiveLoans($at, $data);

        $this->saveModel($at, $data);

        $this->syncRelations($at, $data);

        $this->logRelationChanges($at, $data, $oldDef, $oldTrainings);

        $this->runInspection($at, $data);

        return $this->loadFreshRelations($at);
    }

    private function validateBusinessRules(AssistiveTechnology $at, array $data): void
    {
        $isDigital = $data['is_digital'] ?? $at->is_digital ?? false;
        $isLoanable = $data['is_loanable'] ?? $at->is_loanable ?? false;
        $quantity = isset($data['quantity']) ? (int)$data['quantity'] : $at->quantity;
        $available = isset($data['quantity_available']) ? (int)$data['quantity_available'] : $at->quantity_available;

        if (isset($data['deficiencies']) && empty($data['deficiencies'])) {
            throw new InvalidArgumentException("Selecione pelo menos um público-alvo.");
        }

        if (!$isDigital && $quantity <= 0) {
            throw new DomainException("Para recursos físicos, a quantidade deve ser no mínimo 1.");
        }

        if ($isLoanable && $quantity <= 0) {
            throw new DomainException("Recursos marcados como emprestáveis devem ter quantidade maior que zero.");
        }

        if ($available > $quantity) {
            throw new DomainException("A quantidade disponível ({$available}) não pode ser maior que a quantidade total ({$quantity}).");
        }
    }

    private function captureOriginalState(AssistiveTechnology $at): array
    {
        $oldDeficiencies = $at->exists
            ? $at->deficiencies()->pluck('deficiencies.id')->toArray()
            : [];

        $oldTrainings = $at->exists
            ? $at->trainings()->pluck('id')->toArray()
            : [];

        return [$oldDeficiencies, $oldTrainings];
    }

    private function processStock(AssistiveTechnology $at, array $data): array
    {
        if (isset($data['quantity'])) {
            $this->loanService->validateStockAvailability($at, (int)$data['quantity']);
        }

        return $this->loanService->calculateStockForLoan($at, $data);
    }

    private function saveModel(AssistiveTechnology $at, array $data): void
    {
        if (!$at->exists && empty($data['status'])) {
            $data['status'] = ResourceStatus::AVAILABLE->value;
        }

        $at->fill($data)->save();
    }

    protected function syncRelations(AssistiveTechnology $at, array $data): void
    {
        if (isset($data['deficiencies'])) {
            $at->deficiencies()->sync($data['deficiencies']);
        }

        if ($at->exists && isset($data['trainings'])) {
            /* Treinamentos são recriados integralmente para simplificar o
               gerenciamento de versões e arquivos vinculados em lote. */
            $at->trainings()->delete();

            foreach ($data['trainings'] as $training) {
                $t = $at->trainings()->create([
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

    private function validateStatusChangeWithActiveLoans(AssistiveTechnology $at, array $data): void
    {
        if (!$at->exists || !isset($data['status'])) return;

        $hasActiveLoans = $at->loans()->whereNull('return_date')->exists();

        /* Bloqueamos a mudança de status (ex: para Manutenção) se houver
           empréstimos em aberto para evitar inconsistência no inventário. */
        if ($hasActiveLoans && $at->status->value != $data['status']) {
            throw new CannotChangeStatusWithActiveLoansException();
        }
    }

    private function runInspection(AssistiveTechnology $at, array $data): void
    {
        $this->inspectionService->createInspectionForModel($at, $data);
    }

    private function loadFreshRelations(AssistiveTechnology $at): AssistiveTechnology
    {
        return $at->fresh(['deficiencies', 'trainings']);
    }

    private function logRelationChanges(AssistiveTechnology $at, array $data, array $oldDef, array $oldTrainings): void
    {
        if ($at->wasRecentlyCreated) return;

        if (isset($data['deficiencies'])) {
            $newDef = array_map('intval', $data['deficiencies']);
            /* Ordenamos os arrays para garantir que a comparação identifique
               apenas mudanças reais de conteúdo, ignorando a ordem da requisição. */
            sort($oldDef);
            sort($newDef);
            if ($oldDef !== $newDef) {
                $this->logRelationChange($at, 'deficiencies', $oldDef, $newDef);
            }
        }

        if (isset($data['trainings'])) {
            $newTrain = $at->trainings()->pluck('id')->toArray();
            sort($oldTrainings);
            sort($newTrain);
            if ($oldTrainings !== $newTrain) {
                $this->logRelationChange($at, 'trainings', $oldTrainings, $newTrain);
            }
        }
    }

    protected function logRelationChange(AssistiveTechnology $model, string $field, array $old, array $new): void
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
