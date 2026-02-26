<?php

namespace App\Services\InclusiveRadar;

use App\Enums\Priority;
use App\Models\InclusiveRadar\Barrier;
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
    | ETAPA 1 – CRIAÇÃO
    |--------------------------------------------------------------------------
    */
    public function storeStage1(array $data, int $userId): Barrier
    {
        return DB::transaction(function () use ($data, $userId) {

            $data['step_number'] = 1;
            $data['status'] = BarrierStatus::IDENTIFIED;
            $data['started_by_user_id'] = $userId;
            $data['completed_at'] = now();

            $barrier = Barrier::create($data);

            if (!empty($data['deficiencies'])) {
                $barrier->deficiencies()->sync($data['deficiencies']);
            }

            $this->inspectionService->createForModel($barrier, [
                'status'          => BarrierStatus::IDENTIFIED->value,
                'inspection_date' => $data['inspection_date'],
                'type'            => InspectionType::INITIAL->value,
                'description'     => $data['inspection_description'] ?? 'Vistoria inicial da barreira.',
                'images'          => $data['images'] ?? [],
            ]);

            return $barrier->fresh();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | ETAPA 2 – ANÁLISE
    |--------------------------------------------------------------------------
    */
    public function storeStage2(Barrier $barrier, array $data, int $userId): Barrier
    {
        return DB::transaction(function () use ($barrier, $data, $userId) {

            $this->validateStepProgression($barrier, 2);

            if (!empty($data['not_applicable'])) {
                return $this->handleNotApplicable($barrier, $data, $userId);
            }

            // Atualiza apenas os campos permitidos (regra já protegida na Model)
            $barrier->update([
                'analyst_notes'       => $data['analyst_notes'] ?? null,
                'status'              => BarrierStatus::UNDER_ANALYSIS,
                'step_number'         => 2,
                'user_id'             => $userId,
                'completed_at'        => now(),
            ]);

            $this->inspectionService->createForModel($barrier, [
                'status'          => BarrierStatus::UNDER_ANALYSIS->value,
                'inspection_date' => $data['inspection_date'],
                'type'            => InspectionType::PERIODIC->value,
                'description'     => $data['inspection_description'] ?? 'A barreira está em análise.',
                'images'          => $data['images'] ?? [],
            ]);

            return $barrier->fresh();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | ETAPA 3 – PLANO DE AÇÃO
    |--------------------------------------------------------------------------
    */
    public function storeStage3(Barrier $barrier, array $data, int $userId): Barrier
    {
        return DB::transaction(function () use ($barrier, $data, $userId) {

            $this->validateStepProgression($barrier, 3);

            $barrier->update([
                'step_number'               => 3,
                'status'                    => BarrierStatus::IN_PROGRESS,
                'action_plan_description'   => $data['action_plan_description'],
                'intervention_start_date'   => $data['intervention_start_date'],
                'estimated_completion_date' => $data['estimated_completion_date'],
                'estimated_cost'            => $data['estimated_cost'],
                'user_id'                   => $userId,
                'completed_at'              => now(),
            ]);

            $this->inspectionService->createForModel($barrier, [
                'status'          => BarrierStatus::IN_PROGRESS->value,
                'inspection_date' => $data['inspection_date'],
                'type'            => InspectionType::PERIODIC->value,
                'description'     => $data['inspection_description'] ?? 'Plano de tratamento iniciado.',
                'images'          => $data['images'] ?? [],
            ]);

            return $barrier->fresh();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | ETAPA 4 – RESOLUÇÃO
    |--------------------------------------------------------------------------
    */
    public function storeStage4(Barrier $barrier, array $data, int $userId): Barrier
    {
        return DB::transaction(function () use ($barrier, $data, $userId) {

            $this->validateStepProgression($barrier, 4);

            // Regra de atraso
            if (
                $data['resolution_date'] > $barrier->estimated_completion_date
                && empty($data['delay_justification'])
            ) {
                throw new Exception('Justificativa de atraso é obrigatória.');
            }

            $barrier->update([
                'step_number'              => 4,
                'status'                   => BarrierStatus::RESOLVED,
                'actual_cost'              => $data['actual_cost'],
                'resolution_date'          => $data['resolution_date'],
                'delay_justification'      => $data['delay_justification'] ?? null,
                'resolution_summary'       => $data['resolution_summary'],
                'effectiveness_level'      => $data['effectiveness_level'],
                'maintenance_instructions' => $data['maintenance_instructions'] ?? null,
                'validator_id'             => $userId,
                'completed_at'             => now(),
            ]);

            $this->inspectionService->createForModel($barrier, [
                'status'          => BarrierStatus::RESOLVED->value,
                'inspection_date' => $data['inspection_date'],
                'type'            => InspectionType::PERIODIC->value,
                'description'     => $data['inspection_description'] ?? 'Barreira resolvida e validada.',
                'images'          => $data['images'] ?? [],
            ]);

            return $barrier->fresh();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | REGRA DE PROGRESSÃO SEQUENCIAL
    |--------------------------------------------------------------------------
    */
    private function validateStepProgression(Barrier $barrier, int $requestedStep): void
    {
        if ($barrier->step_number >= $requestedStep) {
            throw new Exception("A Etapa {$requestedStep} já foi concluída.");
        }

        if ($barrier->step_number !== $requestedStep - 1) {
            throw new Exception(
                "Não é possível avançar para a Etapa {$requestedStep} sem concluir a Etapa " . ($requestedStep - 1) . "."
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | ENCERRAMENTO COMO NÃO APLICÁVEL (STEP 2)
    |--------------------------------------------------------------------------
    */
    private function handleNotApplicable(Barrier $barrier, array $data, int $userId): Barrier
    {
        if (empty($data['justificativa_encerramento'])) {
            throw new Exception('Justificativa é obrigatória para encerrar como não aplicável.');
        }

        $barrier->update([
            'step_number'                => 2,
            'status'                     => BarrierStatus::NOT_APPLICABLE,
            'justificativa_encerramento' => $data['justificativa_encerramento'],
            'completed_at'               => now(),
            'user_id'                    => $userId,
        ]);

        $this->inspectionService->createForModel($barrier, [
            'status'          => BarrierStatus::NOT_APPLICABLE->value,
            'inspection_date' => $data['inspection_date'],
            'type'            => InspectionType::PERIODIC->value,
            'description'     => $data['inspection_description'] ?? 'Barreira encerrada como não aplicável.',
            'images'          => $data['images'] ?? [],
        ]);

        return $barrier->fresh();
    }
}
