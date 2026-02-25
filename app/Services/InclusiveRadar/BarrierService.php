<?php

namespace App\Services\InclusiveRadar;

use App\Enums\Priority;
use App\Models\InclusiveRadar\Barrier;
use App\Models\InclusiveRadar\BarrierStage;
use App\Enums\InclusiveRadar\BarrierStatus;
use App\Enums\InclusiveRadar\InspectionType;
use Illuminate\Support\Facades\DB;
use Exception;

class BarrierService
{
    public function __construct(
        protected InspectionService $inspectionService
    ) {}

    /*
    |--------------------------------------------------------------------------
    | ETAPA 1 – IDENTIFICADA (O Antigo "Create")
    |--------------------------------------------------------------------------
    */
    public function storeStage1(array $data, int $userId): Barrier
    {
        return DB::transaction(function () use ($data, $userId) {
            // 1. Cria a Barreira com todos os campos da Etapa 1
            $barrier = Barrier::create($data);

            if (isset($data['deficiencies'])) {
                $barrier->deficiencies()->sync($data['deficiencies']);
            }

            // 2. Cria o registro da Etapa 1
            $barrier->stages()->create([
                'step_number'        => 1,
                'status'             => BarrierStatus::IDENTIFIED,
                'started_by_user_id' => $userId,
                'completed_at'       => now(), // Já nasce concluída para liberar a Etapa 2
            ]);

            // 3. Vistoria Inicial Obrigatória
            $this->inspectionService->createForModel($barrier, [
                'status'          => BarrierStatus::IDENTIFIED->value,
                'inspection_date' => $data['inspection_date'] ?? now(),
                'type'            => InspectionType::INITIAL->value,
                'description'     => $data['description'],
                'images'          => $data['images'] ?? [],
            ]);

            return $barrier->fresh();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | ETAPA 2 – EM ANÁLISE
    |--------------------------------------------------------------------------
    */
    public function storeStage2(Barrier $barrier, array $data, int $userId): BarrierStage
    {
        return DB::transaction(function () use ($barrier, $data, $userId) {
            $this->checkStageAvailability($barrier, 2);

            // Se o usuário clicou em "Não se Aplica" (Encerramento)
            if (isset($data['not_applicable']) && $data['not_applicable'] == true) {
                return $this->handleNotApplicable($barrier, $data, $userId);
            }

            // Atualiza campos permitidos na análise (Refinamento)
            $this->updateBarrierFromAnalysis($barrier, $data);

            // Cria a Etapa 2 e finaliza
            $stage2 = $barrier->stages()->create([
                'step_number'        => 2,
                'status'             => BarrierStatus::UNDER_ANALYSIS,
                'started_by_user_id' => $userId,
                'completed_at'       => now(),
                'analyst_notes'      => $data['analyst_notes'] ?? null,
            ]);

            // Vistoria opcional de análise
            $this->createOptionalInspection($barrier, $data, BarrierStatus::UNDER_ANALYSIS);

            return $stage2;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | ETAPA 3 – EM TRATAMENTO
    |--------------------------------------------------------------------------
    */
    public function storeStage3(Barrier $barrier, array $data, int $userId): BarrierStage
    {
        return DB::transaction(function () use ($barrier, $data, $userId) {
            $this->checkStageAvailability($barrier, 3);

            $stage3 = $barrier->stages()->create([
                'step_number'               => 3,
                'status'                    => BarrierStatus::IN_PROGRESS,
                'started_by_user_id'        => $userId,
                'completed_at'              => now(),
                'action_plan_description'   => $data['action_plan_description'],
                'intervention_start_date'   => $data['intervention_start_date'],
                'estimated_completion_date' => $data['estimated_completion_date'],
                'estimated_cost'            => $data['estimated_cost'],
            ]);

            $this->inspectionService->createForModel($barrier, [
                'status'          => BarrierStatus::IN_PROGRESS->value,
                'inspection_date' => now(),
                'type'            => InspectionType::PERIODIC->value,
                'description'     => 'Plano de tratamento iniciado.',
                'images'          => $data['images'] ?? [],
            ]);

            return $stage3;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | ETAPA 4 – RESOLVIDA
    |--------------------------------------------------------------------------
    */
    public function storeStage4(Barrier $barrier, array $data, int $userId): BarrierStage
    {
        return DB::transaction(function () use ($barrier, $data, $userId) {
            $this->checkStageAvailability($barrier, 4);

            $lastStage = $barrier->stages()->where('step_number', 3)->first();

            // Valida atraso
            if ($lastStage && $data['resolution_date'] > $lastStage->estimated_completion_date) {
                if (empty($data['delay_justification'])) {
                    throw new Exception('Justificativa de atraso é obrigatória.');
                }
            }

            $stage4 = $barrier->stages()->create([
                'step_number'              => 4,
                'status'                   => BarrierStatus::RESOLVED,
                'started_by_user_id'       => $userId,
                'completed_at'             => now(),
                'actual_cost'              => $data['actual_cost'],
                'resolution_date'          => $data['resolution_date'],
                'delay_justification'      => $data['delay_justification'] ?? null,
                'resolution_summary'       => $data['resolution_summary'],
                'effectiveness_level'      => $data['effectiveness_level'],
                'validator_id'             => $userId, // Quem encerrou
                'maintenance_instructions' => $data['maintenance_instructions'] ?? null,
            ]);

            $this->inspectionService->createForModel($barrier, [
                'status'          => BarrierStatus::RESOLVED->value,
                'inspection_date' => $data['resolution_date'],
                'type'            => InspectionType::PERIODIC->value,
                'description'     => 'Barreira resolvida e validada.',
                'images'          => $data['images'] ?? [],
            ]);

            return $stage4;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | AUXILIARES DE REGRA DE NEGÓCIO
    |--------------------------------------------------------------------------
    */

    private function checkStageAvailability(Barrier $barrier, int $requestedStep): void
    {
        if ($barrier->stages()->where('step_number', $requestedStep)->exists()) {
            throw new Exception("A Etapa {$requestedStep} já foi preenchida e não pode ser editada.");
        }

        // Garante que a etapa anterior existe
        if ($requestedStep > 1 && !$barrier->stages()->where('step_number', $requestedStep - 1)->exists()) {
            throw new Exception("Você não pode pular para a Etapa {$requestedStep} sem concluir a anterior.");
        }
    }

    private function updateBarrierFromAnalysis(Barrier $barrier, array $data): void
    {
        $fillable = [
            'description'         => $data['description'] ?? $barrier->description,
            'barrier_category_id' => $data['barrier_category_id'] ?? $barrier->barrier_category_id,
            'priority'            => isset($data['priority']) ? Priority::from($data['priority']) : $barrier->priority,
        ];

        // Se não for anônimo, permite refinar os afetados
        if (!$barrier->is_anonymous) {
            $fillable['affected_student_id']      = $data['affected_student_id'] ?? $barrier->affected_student_id;
            $fillable['affected_professional_id'] = $data['affected_professional_id'] ?? $barrier->affected_professional_id;
            $fillable['affected_person_name']     = $data['affected_person_name'] ?? $barrier->affected_person_name;
            $fillable['affected_person_role']     = $data['affected_person_role'] ?? $barrier->affected_person_role;
        }

        $barrier->update($fillable);
    }

    private function handleNotApplicable(Barrier $barrier, array $data, int $userId): BarrierStage
    {
        if (empty($data['justificativa_encerramento'])) {
            throw new Exception('Justificativa é obrigatória para encerrar como não aplicável.');
        }

        return $barrier->stages()->create([
            'step_number'                => 2,
            'status'                     => BarrierStatus::NOT_APPLICABLE,
            'started_by_user_id'         => $userId,
            'completed_at'               => now(),
            'justificativa_encerramento' => $data['justificativa_encerramento'],
        ]);
    }

    protected function createOptionalInspection(Barrier $barrier, array $data, BarrierStatus $status): void
    {
        if (!empty($data['inspection_description']) || !empty($data['images'])) {
            $this->inspectionService->createForModel($barrier, [
                'status'          => $status->value,
                'inspection_date' => now(),
                'type'            => InspectionType::PERIODIC->value,
                'description'     => $data['inspection_description'] ?? 'Vistoria técnica de análise.',
                'images'          => $data['images'] ?? [],
            ]);
        }
    }
}
