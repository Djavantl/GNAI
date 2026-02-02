<?php

namespace App\Http\Requests\SpecializedEducationalSupport;

use Illuminate\Foundation\Http\FormRequest;

class SessionRecordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'attendance_sessions_id' => ['required', 'exists:attendance_sessions,id'],

            // Controle temporal
            'record_date' => ['nullable', 'date'],
            'duration' => ['required', 'string', 'max:50'],

            // Atividades e estratégias
            'activities_performed' => ['required', 'string'],
            'strategies_used' => ['nullable', 'string'],
            'resources_used' => ['nullable', 'string'],
            'adaptations_made' => ['nullable', 'string'],

            // Comportamento e participação
            'student_participation' => ['required', 'string', 'max:50'],
            'engagement_level' => ['nullable', 'string', 'max:50'],
            'observed_behavior' => ['nullable', 'string'],
            'response_to_activities' => ['nullable', 'string'],

            // Desenvolvimento / evolução
            'development_evaluation' => ['required', 'string'],
            'progress_indicators' => ['nullable', 'string'],

            // Encaminhamentos
            'recommendations' => ['nullable', 'string'],
            'next_session_adjustments' => ['nullable', 'string'],
            'external_referral_needed' => ['nullable', 'boolean'],
            'general_observations' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'session_id.required' => 'A sessão é obrigatória.',
            'session_id.exists' => 'A sessão informada não existe.',

            'duration.required' => 'A duração do atendimento é obrigatória.',

            'activities_performed.required' => 'As atividades realizadas são obrigatórias.',

            'student_participation.required' => 'Informe a participação do aluno.',

            'development_evaluation.required' => 'A avaliação do desenvolvimento é obrigatória.',
        ];
    }
}
