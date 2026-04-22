<?php

namespace App\Http\Requests\SpecializedEducationalSupport;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StudentDeficienciesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $student = $this->route('student');
        $studentId = $student instanceof \App\Models\SpecializedEducationalSupport\Student
            ? $student->id
            : $student;

        return [
            'deficiency_id' => [
                'required',
                Rule::unique('students_deficiencies')
                    ->where('student_id', $studentId),
            ],
            'severity' => ['nullable', 'in:mild,moderate,severe'],
            'uses_support_resources' => ['boolean'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'deficiency_id.required' => 'O perfil de atendimento é obrigatório.',
            'deficiency_id.unique' => 'Este aluno já possui o perfil de atendimento selecionado.',
            'severity.in' => 'A severidade selecionada é inválida.',
            'uses_support_resources.boolean' => 'O campo de recursos de apoio é inválido.',
            'notes.string' => 'As observações devem ser informadas em um texto válido.',
            'notes.max' => 'As observações não podem ultrapassar 1000 caracteres.',
        ];
    }
}
