<?php

namespace App\Services\InclusiveRadar;

use App\Exceptions\InclusiveRadar\CannotChangeStatusWithActiveLoansException;
use App\Exceptions\InclusiveRadar\CannotDeleteWithActiveLoansException;
use App\Models\AuditLog;
use App\Models\InclusiveRadar\AssistiveTechnology;
use App\Enums\InclusiveRadar\ResourceStatus;
use Illuminate\Support\Facades\DB;

/**
 * Service responsável pela gestão de recursos de Tecnologia Assistiva (TA).
 */
class AssistiveTechnologyService
{
    public function __construct(
        protected InspectionService $inspectionService,
        protected LoanService $loanService,
    ) {}

    /**
     * Cria um novo recurso de Tecnologia Assistiva.
     */
    public function store(array $data): AssistiveTechnology
    {
        return DB::transaction(
            fn() => $this->persist(new AssistiveTechnology(), $data)
        );
    }

    /**
     * Atualiza um recurso de Tecnologia Assistiva existente.
     */
    public function update(AssistiveTechnology $assistiveTechnology, array $data): AssistiveTechnology
    {
        return DB::transaction(
            fn() => $this->persist($assistiveTechnology, $data)
        );
    }

    /**
     * Alterna a disponibilidade ativa/inativa do recurso no sistema.
     */
    public function toggleActive(AssistiveTechnology $assistiveTechnology): AssistiveTechnology
    {
        return DB::transaction(function () use ($assistiveTechnology) {
            $assistiveTechnology->update([
                'is_active' => !$assistiveTechnology->is_active
            ]);
            return $assistiveTechnology;
        });
    }

    /**
     * Remove o recurso, validando se não há pendências de empréstimo.
     * * @throws CannotDeleteWithActiveLoansException
     */
    public function delete(AssistiveTechnology $assistiveTechnology): void
    {
        DB::transaction(function () use ($assistiveTechnology) {
            // Travas de segurança: impede a exclusão se houver itens que ainda não retornaram
            if ($assistiveTechnology->loans()->whereNull('return_date')->exists()) {
                throw new CannotDeleteWithActiveLoansException();
            }
            $assistiveTechnology->delete();
        });
    }

    /**
     * Centraliza o fluxo de persistência, estoque e sincronização de relações.
     */
    protected function persist(AssistiveTechnology $at, array $data): AssistiveTechnology
    {
        // Captura do estado prévio para comparar mudanças e gerar logs de auditoria
        [$oldDef, $oldTrainings] = $this->captureOriginalState($at);

        // Valida se a quantidade atual suporta as regras de estoque definidas
        $data = $this->processStock($at, $data);

        // Impede mudança de status (ex: para manutenção) se houver empréstimos ativos
        $this->validateStatusChangeWithActiveLoans($at, $data);

        $this->saveModel($at, $data);

        $this->syncRelations($at, $data);

        // Registra o histórico de alterações em deficiências e treinamentos
        $this->logRelationChanges($at, $data, $oldDef, $oldTrainings);

        // Gera automaticamente o registro de vistoria/inspeção técnica
        $this->runInspection($at, $data);

        return $this->loadFreshRelations($at);
    }

    /**
     * Obtém os IDs atuais das relações para detecção de mudanças (Auditoria).
     */
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

    /**
     * Processa a disponibilidade física do recurso de tecnologia assistiva.
     */
    private function processStock(AssistiveTechnology $at, array $data): array
    {
        if (isset($data['quantity'])) {
            $this->loanService->validateStockAvailability($at, (int)$data['quantity']);
        }

        return $this->loanService->calculateStockForLoan($at, $data);
    }

    /**
     * Persiste os dados básicos e garante o status inicial se necessário.
     */
    private function saveModel(AssistiveTechnology $at, array $data): void
    {
        if (!$at->exists && empty($data['status'])) {
            $data['status'] = ResourceStatus::AVAILABLE->value;
        }

        $at->fill($data)->save();
    }

    /**
     * Sincroniza vínculos de deficiências e reconstrói a lista de treinamentos.
     */
    protected function syncRelations(AssistiveTechnology $at, array $data): void
    {
        if (isset($data['deficiencies'])) {
            $at->deficiencies()->sync($data['deficiencies']);
        }

        if (!empty($data['trainings'])) {
            // Removemos os antigos para reconstruir com as novas URLs e arquivos enviados
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

    /**
     * Validação de integridade: evita inconsistência entre status do recurso e empréstimos.
     * * @throws CannotChangeStatusWithActiveLoansException
     */
    private function validateStatusChangeWithActiveLoans(AssistiveTechnology $at, array $data): void
    {
        if (!$at->exists || !isset($data['status'])) return;

        $hasActiveLoans = $at->loans()->whereNull('return_date')->exists();

        if ($hasActiveLoans && $at->status->value != $data['status']) {
            throw new CannotChangeStatusWithActiveLoansException();
        }
    }

    /**
     * Registra o log de inspeção técnica através do InspectionService.
     */
    private function runInspection(AssistiveTechnology $at, array $data): void
    {
        $this->inspectionService->createInspectionForModel($at, $data);
    }

    /**
     * Recarrega o modelo com os relacionamentos atualizados para o retorno.
     */
    private function loadFreshRelations(AssistiveTechnology $at): AssistiveTechnology
    {
        return $at->fresh(['deficiencies', 'trainings']);
    }

    /**
     * Analisa e registra o log de auditoria caso as relações tenham mudado.
     */
    private function logRelationChanges(AssistiveTechnology $at, array $data, array $oldDef, array $oldTrainings): void
    {
        // Se o registro é novo, a auditoria de criação já cobre o histórico
        if ($at->wasRecentlyCreated) return;

        if (isset($data['deficiencies'])) {
            $newDef = array_map('intval', $data['deficiencies']);
            sort($oldDef); sort($newDef);
            if ($oldDef !== $newDef) {
                $this->logRelationChange($at, 'deficiencies', $oldDef, $newDef);
            }
        }

        if (isset($data['trainings'])) {
            $newTrain = $at->trainings()->pluck('id')->toArray();
            sort($oldTrainings); sort($newTrain);
            if ($oldTrainings !== $newTrain) {
                $this->logRelationChange($at, 'trainings', $oldTrainings, $newTrain);
            }
        }
    }

    /**
     * Cria um registro de auditoria detalhando o estado antigo e o novo.
     */
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
