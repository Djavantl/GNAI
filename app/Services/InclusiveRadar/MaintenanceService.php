<?php

namespace App\Services\InclusiveRadar;

use App\Models\InclusiveRadar\AccessibleEducationalMaterial;
use App\Models\InclusiveRadar\Maintenance;
use App\Models\InclusiveRadar\MaintenanceStage;
use App\Models\InclusiveRadar\AssistiveTechnology;
use App\Enums\InclusiveRadar\InspectionType;
use App\Enums\InclusiveRadar\MaintenanceStatus;
use App\Enums\InclusiveRadar\ConservationState;
use App\Models\InclusiveRadar\ResourceStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Exception;

class MaintenanceService
{
    public function __construct(
        protected InspectionService $inspectionService
    ) {}

    /*
    |--------------------------------------------------------------------------
    | FLUXO DE VIDA DA MANUTENÇÃO (ETAPAS)
    |--------------------------------------------------------------------------
    | Gerencia o ciclo desde a abertura do chamado até o retorno ao estoque.
    */

    /**
     * GATILHO INICIAL – ABERTURA DE SOLICITAÇÃO
     * * IMPORTÂNCIA: Inicia formalmente o fluxo de recuperação técnica do recurso.
     * * LÓGICA: Atualiza o estado de conservação para 'RUIM' (BAD) e cria o registro
     * de manutenção pendente para triagem pela equipe responsável.
     */
    public function openMaintenanceRequest($resource): Maintenance
    {
        return DB::transaction(function () use ($resource) {
            $resource->update(['conservation_state' => ConservationState::BAD]);

            return $resource->maintenances()->create([
                'status' => MaintenanceStatus::PENDING,
            ]);
        });
    }

    /**
     * ETAPA 1 – DIAGNÓSTICO E ESTIMATIVA
     * * IMPORTÂNCIA: Documenta o dano identificado e o custo previsto para o reparo.
     * * SEGURANÇA: Impede alterações após a conclusão desta etapa para garantir a
     * integridade do histórico de triagem inicial.
     */
    public function saveStage1(Maintenance $maintenance, array $data, int $userId, bool $finalize = true): MaintenanceStage
    {
        return DB::transaction(function () use ($maintenance, $data, $userId, $finalize) {
            $resource = $maintenance->maintainable;
            $stage1 = $maintenance->stages()->firstOrNew(['step_number' => 1]);

            if ($stage1->completed_at) {
                throw new Exception("Esta etapa já foi concluída e não permite mais alterações.");
            }

            if (!$stage1->started_by_user_id) {
                $stage1->started_by_user_id = $userId;
            }

            if ($finalize) {
                $stage1->user_id      = $userId;
                $stage1->completed_at = now();
            }

            $stage1->estimated_cost     = $data['estimated_cost'] ?? $stage1->estimated_cost;
            $stage1->observation        = $data['observation'] ?? $stage1->observation ?? 'Manutenção iniciada.';
            $stage1->damage_description = $data['damage_description'] ?? $stage1->damage_description;
            $stage1->save();

            if ($resource->conservation_state != ConservationState::BAD->value) {
                $resource->update(['conservation_state' => ConservationState::BAD]);
            }

            return $stage1;
        });
    }

    /**
     * ETAPA 2 – EXECUÇÃO E FINALIZAÇÃO
     * * IMPORTÂNCIA: Conclui o reparo e decide se o item está apto a voltar para uso.
     * * LÓGICA: Ao finalizar, dispara automaticamente uma Vistoria (InspectionService)
     * e, caso o estado seja restaurado (Novo/Bom/Regular), devolve o status de
     * 'DISPONÍVEL' ao recurso no inventário.
     */
    public function saveStage2(Maintenance $maintenance, array $data, int $userId, bool $finalize = true): MaintenanceStage
    {
        return DB::transaction(function () use ($maintenance, $data, $userId, $finalize) {
            $resource = $maintenance->maintainable;
            $stage2 = $maintenance->stages()->firstOrNew(['step_number' => 2]);

            if ($stage2->completed_at) {
                throw new Exception("A manutenção já foi finalizada e não permite mais alterações.");
            }

            if (!$stage2->started_by_user_id) {
                $stage2->started_by_user_id = $userId;
            }

            $stage2->real_cost = $data['real_cost'] ?? $stage2->real_cost;
            $stage2->observation = $data['inspection_description'] ?? $stage2->observation;

            if ($finalize) {
                $stage2->user_id = $userId;
                $stage2->completed_at = now();

                $this->inspectionService->createInspectionForModel($resource, [
                    'inspection_date' => $data['inspection_date'] ?? now(),
                    'type'            => InspectionType::MAINTENANCE->value,
                    'state'           => $data['state'] ?? $resource->conservation_state,
                    'description'     => $data['inspection_description'] ?? 'Manutenção finalizada com sucesso.',
                    'images'          => $data['images'] ?? [],
                ]);

                if (in_array($data['state'] ?? null, [
                    ConservationState::NEW->value,
                    ConservationState::GOOD->value,
                    ConservationState::REGULAR->value,
                ])) {
                    $availableStatus = ResourceStatus::where('code', 'available')->first();
                    $resource->update([
                        'conservation_state' => $data['state'],
                        'status_id'          => $availableStatus->id ?? $resource->status_id,
                    ]);
                }

                $maintenance->update(['status' => MaintenanceStatus::COMPLETED]);
            }

            $stage2->save();
            return $stage2;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | CONSULTAS E DASHBOARD
    |--------------------------------------------------------------------------
    */

    /**
     * MONITOR DE MANUTENÇÕES (DASHBOARD)
     * * IMPORTÂNCIA: Fornece uma visão unificada de todos os itens (TA e MPA) que
     * necessitam de atenção técnica.
     * * LÓGICA: Une os resultados de ambas as tabelas (AssistiveTechnology e
     * AccessibleEducationalMaterial), permitindo filtrar por status 'pendente' ou 'concluído'.
     */
    public function maintenanceDashboardResources(array $filters = []): Collection
    {
        $statusFilter = ($filters['status'] ?? '') ?: null;
        $resourceName = $filters['resource'] ?? null;

        $applyFilters = function ($query) use ($statusFilter, $resourceName) {
            // 1. Sempre filtra pelo nome se houver
            $query->filterName($resourceName);

            // 2. Lógica de Filtro de Manutenção
            if ($statusFilter === 'pending') {
                // Apenas recursos que TÊM manutenções não concluídas
                $query->whereHas('maintenances', function ($q) {
                    $q->where('status', '!=', \App\Enums\InclusiveRadar\MaintenanceStatus::COMPLETED->value);
                });
            } elseif ($statusFilter === 'completed') {
                // Apenas recursos que TÊM manutenções concluídas
                $query->whereHas('maintenances', function ($q) {
                    $q->where('status', \App\Enums\InclusiveRadar\MaintenanceStatus::COMPLETED->value);
                });
            } else {
                // MODO TODOS: Traz qualquer recurso que tenha pelo menos uma manutenção no histórico
                $query->has('maintenances');
            }

            return $query->with([
                'maintenances' => fn($q) => $q->orderBy('created_at', 'desc'),
                'maintenances.stages.user',
                'maintenances.stages.starter'
            ]);
        };

        $tas = $applyFilters(AssistiveTechnology::query())->get();
        $mpas = $applyFilters(AccessibleEducationalMaterial::query())->get();

        return $tas->concat($mpas); // concat é mais seguro para coleções de modelos diferentes
    }
}
