<?php

namespace App\Http\Requests\InclusiveRadar;

use App\Enums\InclusiveRadar\BarrierStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use App\Models\InclusiveRadar\BarrierStage;

class BarrierStageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $step = (int) $this->input('step_number');
        $barrierId = $this->input('barrier_id');

        $rules = [
            'barrier_id' => ['required', 'integer', 'exists:barriers,id'],
            'step_number' => ['required', 'integer', 'min:1', 'max:4'],
            'observation' => ['nullable', 'string', 'max:1000'],
            'finalize' => ['nullable', 'in:0,1'],
        ];

        $isStepClosed = BarrierStage::where('barrier_id', $barrierId)
            ->where('step_number', $step)
            ->whereNotNull('completed_at')
            ->exists();

        if ($isStepClosed) {
            return $rules;
        }

        switch ($step) {
            case 1: // Etapa Identificada
                $rules['started_by_user_id'] = ['required', 'exists:users,id'];
                $rules['inspection_date'] = ['required', 'date', 'before_or_equal:today'];
                $rules['description'] = ['required', 'string'];
                break;

            case 2: // Etapa Em Análise
                $rules['user_id'] = ['required', 'exists:users,id'];
                $rules['status'] = ['required', new Enum(BarrierStatus::class)];
                $rules['completed_at'] = ['required', 'date'];
                $rules['analyst_notes'] = ['nullable', 'string', 'max:2000'];

                // Se usuário marcar "Não se aplica", justificativa é obrigatória
                if ($this->input('status') === BarrierStatus::NOT_APPLICABLE->value) {
                    $rules['justificativa_encerramento'] = ['required', 'string', 'max:2000'];
                }
                break;

            case 3: // Etapa Em Tratamento
                $rules['action_plan_description'] = ['required', 'string'];
                $rules['intervention_start_date'] = ['required', 'date'];
                $rules['estimated_completion_date'] = ['required', 'date', 'after_or_equal:intervention_start_date'];
                $rules['estimated_cost'] = ['required', 'numeric', 'min:0'];
                break;

            case 4: // Etapa Resolvida
                $rules['actual_cost'] = ['required', 'numeric', 'min:0'];
                $rules['resolution_date'] = ['required', 'date'];
                $rules['resolution_summary'] = ['required', 'string', 'max:2000'];
                $rules['effectiveness_level'] = ['required', 'string', 'max:255'];
                $rules['validator_id'] = ['required', 'exists:users,id'];

                // Se houver atraso, delay_justification é obrigatória
                if ($this->input('resolution_date') && $this->input('estimated_completion_date') &&
                    $this->input('resolution_date') > $this->input('estimated_completion_date')) {
                    $rules['delay_justification'] = ['required', 'string', 'max:2000'];
                }
                break;
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'barrier_id.required' => 'A barreira é obrigatória.',
            'step_number.required' => 'O número da etapa é obrigatório.',
            'started_by_user_id.required' => 'O usuário que iniciou a etapa é obrigatório.',
            'user_id.required' => 'O usuário que finaliza a etapa é obrigatório.',
            'status.required' => 'O status da etapa é obrigatório.',
            'completed_at.required' => 'A data de conclusão da etapa é obrigatória.',
            'inspection_date.required' => 'A data de vistoria é obrigatória.',
            'description.required' => 'A descrição da barreira é obrigatória.',
            'action_plan_description.required' => 'O plano de ação é obrigatório.',
            'intervention_start_date.required' => 'A data de início da intervenção é obrigatória.',
            'estimated_completion_date.required' => 'A data prevista de conclusão é obrigatória.',
            'estimated_cost.required' => 'O custo estimado é obrigatório.',
            'actual_cost.required' => 'O custo real é obrigatório.',
            'resolution_date.required' => 'A data de resolução é obrigatória.',
            'resolution_summary.required' => 'O resumo da resolução é obrigatório.',
            'effectiveness_level.required' => 'O nível de efetividade é obrigatório.',
            'validator_id.required' => 'O validador é obrigatório.',
            'justificativa_encerramento.required' => 'A justificativa de encerramento é obrigatória.',
            'delay_justification.required' => 'A justificativa do atraso é obrigatória.',
        ];
    }
}
