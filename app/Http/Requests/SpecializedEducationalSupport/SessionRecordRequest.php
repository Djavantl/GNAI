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
            'attendance_session_id.required'   => 'O agendamento do atendimento é obrigatório.',
            'attendance_session_id.exists'     => 'O agendamento do atendimento informado é inválido.',
            'duration.required'             => 'A duração do agendamento é obrigatória.',
            'duration.string'               => 'A duração do agendamento deve ser um texto válido.',
            'duration.max'                  => 'A duração do agendamento não pode ultrapassar 50 caracteres.',
            'activities_performed.required' => 'O relato das atividades realizadas é obrigatório.',
            'activities_performed.string'   => 'O relato das atividades realizadas deve ser um texto válido.',
            'strategies_used.string'        => 'As estratégias utilizadas devem ser informadas em um texto válido.',
            'resources_used.string'         => 'Os recursos utilizados devem ser informados em um texto válido.',
            'general_observations.string'   => 'As observações gerais devem ser informadas em um texto válido.',
            'evaluations.required'          => 'É necessário informar ao menos uma avaliação de aluno.',
            'evaluations.array'             => 'As avaliações dos alunos devem ser informadas em uma lista válida.',
            'evaluations.min'               => 'É necessário informar ao menos uma avaliação de aluno.',
            'evaluations.*.student_id.required' => 'O aluno da avaliação é obrigatório.',
            'evaluations.*.student_id.exists' => 'Um dos alunos informados na avaliação é inválido.',
            'evaluations.*.is_present.boolean' => 'O campo de presença do aluno é inválido.',
            
            // Mensagens específicas com o wildcard
            'evaluations.*.absence_reason.required_if'        => 'A justificativa é obrigatória para alunos ausentes.',
            'evaluations.*.absence_reason.string'             => 'A justificativa de ausência deve ser um texto válido.',
            'evaluations.*.student_participation.required_if'  => 'A participação é obrigatória para alunos presentes.',
            'evaluations.*.student_participation.string'       => 'A participação deve ser informada em um texto válido.',
            'evaluations.*.development_evaluation.required_if' => 'A avaliação de desenvolvimento é obrigatória para alunos presentes.',
            'evaluations.*.development_evaluation.string'      => 'A avaliação de desenvolvimento deve ser informada em um texto válido.',
            'evaluations.*.adaptations_made.string'            => 'As adaptações realizadas devem ser informadas em um texto válido.',
            'evaluations.*.progress_indicators.string'         => 'Os indicadores de progresso devem ser informados em um texto válido.',
            'evaluations.*.recommendations.string'             => 'As recomendações devem ser informadas em um texto válido.',
            'evaluations.*.next_session_adjustments.string'    => 'Os ajustes para o próximo atendimento devem ser informados em um texto válido.',
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
