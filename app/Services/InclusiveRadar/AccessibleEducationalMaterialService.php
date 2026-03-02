<?php

namespace App\Services\InclusiveRadar;

use App\Exceptions\InclusiveRadar\CannotChangeStatusWithActiveLoansException;
use App\Exceptions\InclusiveRadar\CannotDeleteWithActiveLoansException;
use App\Models\AuditLog;
use App\Models\InclusiveRadar\AccessibleEducationalMaterial;
use App\Enums\InclusiveRadar\ResourceStatus;
use Illuminate\Support\Facades\DB;

/**
 * Service responsável pela gestão de materiais educacionais acessíveis.
 */
class AccessibleEducationalMaterialService
{
    public function __construct(
        protected InspectionService $inspectionService,
        protected LoanService $loanService,
    ) {}

    /**
     * Cria um novo material educacional.
     */
    public function store(array $data): AccessibleEducationalMaterial
    {
        return DB::transaction(
            fn() => $this->persist(new AccessibleEducationalMaterial(), $data)
        );
    }

    /**
     * Atualiza um material educacional existente.
     */
    public function update(AccessibleEducationalMaterial $material, array $data): AccessibleEducationalMaterial
    {
        return DB::transaction(
            fn() => $this->persist($material, $data)
        );
    }

    /**
     * Alterna o status de ativação do material.
     */
    public function toggleActive(AccessibleEducationalMaterial $material): AccessibleEducationalMaterial
    {
        return DB::transaction(function () use ($material) {
            $material->update([
                'is_active' => !$material->is_active
            ]);
            return $material;
        });
    }

    /**
     * Remove o material se não houver empréstimos ativos.
     * * @throws CannotDeleteWithActiveLoansException
     */
    public function delete(AccessibleEducationalMaterial $material): void
    {
        DB::transaction(function () use ($material) {
            // Materiais com empréstimos em aberto (sem data de retorno) não podem ser excluídos
            if ($material->loans()->whereNull('return_date')->exists()) {
                throw new CannotDeleteWithActiveLoansException();
            }
            $material->delete();
        });
    }

    /**
     * Orquestra o fluxo de persistência e sincronização de dados.
     */
    protected function persist(AccessibleEducationalMaterial $material, array $data): AccessibleEducationalMaterial
    {
        // Precisamos do estado anterior para comparar e gerar logs de auditoria precisos
        [$oldDef, $oldFeatures, $oldTrainings] = $this->captureOriginalState($material);

        // Verifica se a quantidade solicitada é compatível com o estoque atual
        $data = $this->processStock($material, $data);

        // Garante que o status do recurso não mude enquanto houverem itens emprestados
        $this->validateStatusChangeWithActiveLoans($material, $data);

        $this->saveModel($material, $data);

        $this->syncRelations($material, $data);

        // Dispara logs apenas para campos que sofreram alteração real
        $this->logRelationChanges($material, $data, $oldDef, $oldFeatures, $oldTrainings);

        // Toda alteração ou criação gera uma nova vistoria técnica automaticamente
        $this->runInspection($material, $data);

        return $this->loadFreshRelations($material);
    }

    /**
     * Captura o estado das relações antes da alteração para fins de auditoria.
     */
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

    /**
     * Valida disponibilidade e processa os dados de estoque.
     */
    private function processStock(AccessibleEducationalMaterial $material, array $data): array
    {
        if (isset($data['quantity'])) {
            $this->loanService->validateStockAvailability($material, (int) $data['quantity']);
        }
        return $this->loanService->calculateStockForLoan($material, $data);
    }

    /**
     * Salva os dados básicos no banco de dados.
     */
    private function saveModel(AccessibleEducationalMaterial $material, array $data): void
    {
        // Novos materiais entram como 'Disponível' por padrão caso nenhum status seja enviado
        if (!$material->exists && empty($data['status'])) {
            $data['status'] = ResourceStatus::AVAILABLE->value;
        }

        $material->fill($data)->save();
    }

    /**
     * Sincroniza deficiências, recursos e treinamentos vinculados.
     */
    protected function syncRelations(AccessibleEducationalMaterial $material, array $data): void
    {
        if (isset($data['deficiencies'])) {
            $material->deficiencies()->sync($data['deficiencies']);
        }

        if (isset($data['accessibility_features'])) {
            $material->accessibilityFeatures()->sync($data['accessibility_features']);
        }

        if (!empty($data['trainings'])) {
            // Recriamos os treinamentos para garantir a integridade dos anexos enviados
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

    /**
     * Impede a alteração de status caso existam empréstimos pendentes.
     * * @throws CannotChangeStatusWithActiveLoansException
     */
    private function validateStatusChangeWithActiveLoans(AccessibleEducationalMaterial $material, array $data): void
    {
        if (!$material->exists || !isset($data['status'])) return;

        $hasActiveLoans = $material->loans()->whereNull('return_date')->exists();

        // Se houver empréstimo ativo, o status (Ex: Disponível para Manutenção) não pode ser trocado
        if ($hasActiveLoans && $material->status->value != $data['status']) {
            throw new CannotChangeStatusWithActiveLoansException();
        }
    }

    /**
     * Dispara a criação de vistoria técnica.
     */
    private function runInspection(AccessibleEducationalMaterial $material, array $data): void
    {
        $this->inspectionService->createInspectionForModel($material, $data);
    }

    /**
     * Recarrega as relações atualizadas do modelo para retorno da API.
     */
    private function loadFreshRelations(AccessibleEducationalMaterial $material): AccessibleEducationalMaterial
    {
        return $material->fresh(['deficiencies', 'accessibilityFeatures', 'trainings.files']);
    }

    /**
     * Avalia e registra mudanças nas relações para auditoria.
     */
    private function logRelationChanges(AccessibleEducationalMaterial $material, array $data, array $oldDef, array $oldFeatures, array $oldTrainings): void
    {
        // Ignoramos auditoria em registros recém-criados para evitar duplicidade com o log de criação
        if ($material->wasRecentlyCreated) return;

        if (isset($data['deficiencies'])) {
            $this->auditIfChanged($material, 'deficiencies', $oldDef, $data['deficiencies']);
        }

        if (isset($data['accessibility_features'])) {
            $this->auditIfChanged($material, 'accessibility_features', $oldFeatures, $data['accessibility_features']);
        }

        if (isset($data['trainings'])) {
            $newTrain = $material->trainings()->pluck('id')->toArray();
            sort($oldTrainings); sort($newTrain);

            // Compara arrays de IDs para detectar mudanças na lista de treinamentos
            if ($oldTrainings !== $newTrain) {
                $this->logRelationChange($material, 'trainings', $oldTrainings, $newTrain);
            }
        }
    }

    /**
     * Compara arrays e registra log se houver diferença.
     */
    protected function auditIfChanged($model, string $field, array $old, ?array $new): void
    {
        if ($new === null) return;
        $new = array_map('intval', $new);
        sort($old); sort($new);

        if ($old !== $new) {
            $this->logRelationChange($model, $field, $old, $new);
        }
    }

    /**
     * Salva o registro de auditoria no banco de dados.
     */
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
