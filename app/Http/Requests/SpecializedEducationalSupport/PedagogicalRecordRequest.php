<?php

namespace App\Http\Requests\SpecializedEducationalSupport;

use Illuminate\Foundation\Http\FormRequest;

class PedagogicalRecordRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_present' => $this->boolean('is_present'),
        ]);
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'attendance_session_id' => ['required', 'exists:attendance_sessions,id'],
            'duration' => ['required', 'string', 'max:50'],
            'is_present' => ['boolean'],
            'absence_reason' => ['required_if:is_present,0', 'nullable', 'string'],
            'planned_performed_activities' => ['required_if:is_present,1', 'nullable', 'string'],
            'pedagogical_record' => ['required_if:is_present,1', 'nullable', 'string'],
            'resources_used' => ['nullable', 'string'],
            'general_observations' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'attendance_session_id.required' => 'O agendamento do atendimento pedagógico é obrigatório.',
            'attendance_session_id.exists' => 'O agendamento informado é inválido.',
            'duration.required' => 'A duração do atendimento é obrigatória.',
            'duration.string' => 'A duração do atendimento deve ser um texto válido.',
            'duration.max' => 'A duração do atendimento não pode ultrapassar 50 caracteres.',
            'is_present.boolean' => 'O campo de presença do aluno é inválido.',
            'absence_reason.required_if' => 'A justificativa é obrigatória para aluno ausente.',
            'absence_reason.string' => 'A justificativa de ausência deve ser um texto válido.',
            'planned_performed_activities.required_if' => 'As atividades planejadas/realizadas são obrigatórias para aluno presente.',
            'planned_performed_activities.string' => 'As atividades planejadas/realizadas devem ser um texto válido.',
            'pedagogical_record.required_if' => 'O registro pedagógico é obrigatório para aluno presente.',
            'pedagogical_record.string' => 'O registro pedagógico deve ser um texto válido.',
            'resources_used.string' => 'Os recursos utilizados devem ser informados em um texto válido.',
            'general_observations.string' => 'As observações gerais devem ser informadas em um texto válido.',
        ];
    }
}
