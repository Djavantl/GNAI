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
}