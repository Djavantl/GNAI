<?php

namespace App\Http\Requests\SpecializedEducationalSupport;

use Illuminate\Foundation\Http\FormRequest;

class SessionRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepara os dados antes da validação.
     * Isso resolve o problema do checkbox não enviar valor quando desmarcado.
     */
    protected function prepareForValidation()
    {
        $evaluations = $this->evaluations;

        if (is_array($evaluations)) {
            foreach ($evaluations as $index => $eval) {
                // Se o checkbox 'is_present' não existir no array, definimos como 0
                $evaluations[$index]['is_present'] = isset($eval['is_present']) ? $eval['is_present'] : 0;
            }
            $this->merge(['evaluations' => $evaluations]);
        }
    }

    public function rules(): array
    {
        return [
            'attendance_session_id' => ['required', 'exists:attendance_sessions,id'],
            'duration'              => ['required', 'string', 'max:50'],
            'activities_performed'  => ['required', 'string'],
            'strategies_used'       => ['nullable', 'string'],
            'resources_used'        => ['nullable', 'string'],
            'general_observations'  => ['nullable', 'string'],

            'evaluations'                => ['required', 'array', 'min:1'],
            'evaluations.*.student_id'   => ['required', 'exists:students,id'],
            
            // Agora garantimos que ele aceite 0, 1, true, false ou "on"
            'evaluations.*.is_present'   => ['boolean'],

            // Regra: Obrigatório se is_present for falso (0)
            'evaluations.*.absence_reason' => [
                'required_if:evaluations.*.is_present,0', 
                'nullable', 
                'string'
            ],

            // Regra: Obrigatórios se is_present for verdadeiro (1)
            'evaluations.*.student_participation' => [
                'required_if:evaluations.*.is_present,1', 
                'nullable', 
                'string'
            ],
            'evaluations.*.development_evaluation' => [
                'required_if:evaluations.*.is_present,1', 
                'nullable', 
                'string'
            ],
            
            'evaluations.*.adaptations_made'         => ['nullable', 'string'],
            'evaluations.*.progress_indicators'      => ['nullable', 'string'],
            'evaluations.*.recommendations'          => ['nullable', 'string'],
            'evaluations.*.next_session_adjustments' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'duration.required'             => 'A duração da sessão é obrigatória.',
            'activities_performed.required' => 'O relato das atividades realizadas é obrigatório.',
            
            // Mensagens específicas com o wildcard
            'evaluations.*.absence_reason.required_if'        => 'A justificativa é obrigatória para alunos ausentes.',
            'evaluations.*.student_participation.required_if'  => 'A participação é obrigatória para alunos presentes.',
            'evaluations.*.development_evaluation.required_if' => 'A avaliação de desenvolvimento é obrigatória para alunos presentes.',
        ];
    }

    public function attributes(): array
    {
        return [
            'evaluations.*.student_id'            => 'aluno',
            'evaluations.*.is_present'            => 'presença',
            'evaluations.*.absence_reason'        => 'justificativa de ausência',
            'evaluations.*.student_participation'  => 'participação',
            'evaluations.*.development_evaluation' => 'avaliação de desenvolvimento',
        ];
    }
}