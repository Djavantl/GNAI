<?php

namespace App\Http\Requests\SpecializedEducationalSupport;

use Illuminate\Foundation\Http\FormRequest;

class SessionRequest extends FormRequest
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
            'professional_id' => ['required', 'exists:professionals,id'],
            'student_ids' => ['required', 'array', 'min:1'],
            'student_ids.*' => ['exists:students,id'],
            'session_date' => ['required', 'date'],
            'start_time'   => ['required', 'date_format:H:i'],
            'end_time'     => ['nullable', 'date_format:H:i', 'after:start_time'],
            'type' => ['required', 'string', 'max:100'],
            'location' => ['nullable', 'string', 'max:255'],
            'session_objective' => ['required', 'string'],
            'status' => ['sometimes'],
        ];
    }

    public function messages(): array
    {
        return [
            'student_ids.required' => 'Selecione ao menos um aluno.',
            'student_ids.*.exists' => 'Um dos alunos informados não existe.',

            'professional_id.required' => 'O profissional é obrigatório.',
            'professional_id.exists' => 'O profissional informado não existe.',

            'session_date.required' => 'A data da sessão é obrigatória.',
            'session_date.date' => 'A data da sessão deve ser válida.',

            'start_time.required' => 'O horário de início é obrigatório.',
            'start_time.date_format' => 'O horário de início deve estar no formato HH:MM.',

            'end_time.after' => 'O horário de término deve ser após o início.',

            'type.required' => 'O tipo de atendimento é obrigatório.',

            'session_objective.required' => 'O objetivo da sessão é obrigatório.',
            'cancellation_reason.required_if' => 'Informe o motivo do cancelamento.',
        ];
    }
}
