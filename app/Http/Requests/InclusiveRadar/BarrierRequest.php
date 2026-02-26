<?php

namespace App\Http\Requests\InclusiveRadar;

use App\Enums\InclusiveRadar\BarrierStatus;
use App\Enums\InclusiveRadar\EffectivenessLevel;
use App\Enums\Priority;
use App\Models\InclusiveRadar\Barrier;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class BarrierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $barrier = $this->route('barrier');
        $barrierId = $barrier instanceof Barrier ? $barrier->id : $barrier;

        $stepNumber = $this->input('step_number') ?? match (true) {
            $this->routeIs('*.create') => 1,
            $this->routeIs('*.store') => 1,
            $this->routeIs('*.saveStage1') => 1,
            $this->routeIs('*.saveStage2') => 2,
            $this->routeIs('*.saveStage3') => 3,
            $this->routeIs('*.saveStage4') => 4,
            default => 1,
        };

        $this->merge([
            'barrier_id' => $barrierId,
            'step_number' => (int) $stepNumber,
            'user_id' => $this->user_id ?? auth()->id(),
            'validator_id' => $this->validator_id ?? auth()->id(),
            'started_by_user_id' => $this->started_by_user_id ?? auth()->id(),
            'status' => $this->status ?? match((int)$stepNumber) {
                    2 => BarrierStatus::UNDER_ANALYSIS->value,
                    3 => BarrierStatus::IN_PROGRESS->value,
                    4 => BarrierStatus::RESOLVED->value,
                    default => null
                }
        ]);
    }

    public function rules(): array
    {
        $step = (int) $this->step_number;

        $rules = [
            'step_number' => ['required', 'integer', 'min:1', 'max:4'],
            'observation' => ['nullable', 'string', 'max:1000'],

            // Inspeção (obrigatória em TODAS as etapas)
            'inspection_date' => ['required', 'date', 'before_or_equal:today'],
            'inspection_description' => ['nullable', 'string', 'max:1000'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ];

        /*
        |--------------------------------------------------------------------------
        | STEP 1 - CRIAÇÃO DA BARREIRA
        |--------------------------------------------------------------------------
        */
        if ($step === 1) {

            $rules += [

                // Dados principais
                'name' => ['required', 'string', 'max:255'],
                'description' => ['nullable', 'string'],

                'institution_id' => ['required', 'exists:institutions,id'],
                'barrier_category_id' => ['required', 'exists:barrier_categories,id'],
                'location_id' => ['nullable', 'exists:locations,id'],

                // Localização manual (caso não use location_id)
                'latitude' => ['nullable', 'numeric', 'between:-90,90'],
                'longitude' => ['nullable', 'numeric', 'between:-180,180'],

                'location_specific_details' => ['nullable', 'string', 'max:255'],

                // Pessoa afetada
                'affected_student_id' => ['nullable', 'exists:students,id'],
                'affected_professional_id' => ['nullable', 'exists:professionals,id'],
                'affected_person_name' => ['nullable', 'string', 'max:255'],
                'affected_person_role' => ['nullable', 'string', 'max:255'],
                'is_anonymous' => ['boolean'],
                'priority' => ['required', new Enum(Priority::class)],
                'identified_at' => ['required', 'date'],

                // Deficiências
                'deficiencies' => ['required', 'array', 'min:1'],
                'deficiencies.*' => ['exists:deficiencies,id'],
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | STEP 2 - ANÁLISE
        |--------------------------------------------------------------------------
        */
        if ($step === 2) {
            $rules += [
                'status' => ['required', 'in:identified,under_analysis,resolved,closed'],
                'analyst_notes' => ['required', 'string', 'max:1000'],
                'justificativa_encerramento' => ['nullable', 'string'],
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | STEP 3 - PLANO DE AÇÃO
        |--------------------------------------------------------------------------
        */
        if ($step === 3) {
            $rules += [
                'action_plan_description' => ['required', 'string'],
                'intervention_start_date' => ['required', 'date'],
                'estimated_completion_date' => ['required', 'date', 'after_or_equal:intervention_start_date'],
                'estimated_cost' => ['required', 'numeric', 'min:0'],
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | STEP 4 - RESOLUÇÃO
        |--------------------------------------------------------------------------
        */
        if ($step === 4) {
            $rules += [
                'actual_cost' => ['required', 'numeric', 'min:0'],
                'resolution_date' => ['required', 'date'],
                'resolution_summary' => ['required', 'string'],
                'effectiveness_level' => ['required', 'in:low,medium,high'],
                'delay_justification' => ['nullable', 'string'],
                'maintenance_instructions' => ['nullable', 'string'],
            ];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | GERAIS
            |--------------------------------------------------------------------------
            */
            'step_number.required' => 'A etapa é obrigatória.',
            'step_number.integer' => 'A etapa deve ser um número válido.',
            'step_number.min' => 'A etapa mínima é 1.',
            'step_number.max' => 'A etapa máxima é 4.',

            'observation.max' => 'A observação pode ter no máximo 1000 caracteres.',

            /*
            |--------------------------------------------------------------------------
            | INSPEÇÃO (todas as etapas)
            |--------------------------------------------------------------------------
            */
            'inspection_date.required' => 'A data da inspeção é obrigatória.',
            'inspection_date.date' => 'Informe uma data válida para a inspeção.',
            'inspection_date.before_or_equal' => 'A data da inspeção não pode ser futura.',

            'inspection_description.max' => 'A descrição da inspeção pode ter no máximo 1000 caracteres.',

            'images.array' => 'As imagens devem ser enviadas corretamente.',
            'images.*.image' => 'Cada arquivo deve ser uma imagem válida.',
            'images.*.mimes' => 'As imagens devem estar nos formatos: jpeg, png, jpg ou webp.',
            'images.*.max' => 'Cada imagem deve ter no máximo 5MB.',

            /*
            |--------------------------------------------------------------------------
            | STEP 1 - BARREIRA
            |--------------------------------------------------------------------------
            */
            'name.required' => 'O nome da barreira é obrigatório.',
            'name.max' => 'O nome da barreira pode ter no máximo 255 caracteres.',

            'institution_id.required' => 'A instituição é obrigatória.',
            'institution_id.exists' => 'A instituição selecionada não é válida.',

            'barrier_category_id.required' => 'A categoria da barreira é obrigatória.',
            'barrier_category_id.exists' => 'A categoria selecionada não é válida.',

            'location_id.exists' => 'O local selecionado não é válido.',

            'latitude.numeric' => 'A latitude deve ser um número válido.',
            'latitude.between' => 'A latitude deve estar entre -90 e 90.',

            'longitude.numeric' => 'A longitude deve ser um número válido.',
            'longitude.between' => 'A longitude deve estar entre -180 e 180.',

            'priority.required' => 'A prioridade é obrigatória.',
            'priority.Illuminate\Validation\Rules\Enum' => 'A prioridade selecionada é inválida.',

            'identified_at.required' => 'A data de identificação é obrigatória.',
            'identified_at.date' => 'Informe uma data válida para identificação.',

            'deficiencies.required' => 'Selecione pelo menos uma deficiência.',
            'deficiencies.array' => 'As deficiências devem ser enviadas corretamente.',
            'deficiencies.min' => 'Selecione pelo menos uma deficiência.',
            'deficiencies.*.exists' => 'Uma das deficiências selecionadas não é válida.',

            /*
            |--------------------------------------------------------------------------
            | STEP 2
            |--------------------------------------------------------------------------
            */
            'status.required' => 'O status é obrigatório.',
            'status.in' => 'O status informado não é válido.',

            /*
            |--------------------------------------------------------------------------
            | STEP 3
            |--------------------------------------------------------------------------
            */
            'action_plan_description.required' => 'A descrição do plano de ação é obrigatória.',

            'intervention_start_date.required' => 'A data de início da intervenção é obrigatória.',
            'intervention_start_date.date' => 'Informe uma data válida para início da intervenção.',

            'estimated_completion_date.required' => 'A data estimada de conclusão é obrigatória.',
            'estimated_completion_date.after_or_equal' => 'A data de conclusão deve ser igual ou posterior ao início da intervenção.',

            'estimated_cost.required' => 'O custo estimado é obrigatório.',
            'estimated_cost.numeric' => 'O custo estimado deve ser numérico.',
            'estimated_cost.min' => 'O custo estimado não pode ser negativo.',

            /*
            |--------------------------------------------------------------------------
            | STEP 4
            |--------------------------------------------------------------------------
            */
            'actual_cost.required' => 'O custo real é obrigatório.',
            'actual_cost.numeric' => 'O custo real deve ser numérico.',
            'actual_cost.min' => 'O custo real não pode ser negativo.',

            'resolution_date.required' => 'A data de resolução é obrigatória.',
            'resolution_date.date' => 'Informe uma data válida para resolução.',

            'resolution_summary.required' => 'O resumo da resolução é obrigatório.',

            'effectiveness_level.required' => 'O nível de efetividade é obrigatório.',
            'effectiveness_level.in' => 'O nível de efetividade deve ser baixo, médio ou alto.',
        ];
    }
}
