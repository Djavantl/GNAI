<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $student = $this->route('student');
        $personId = $student?->person_id;
        $studentId = $student?->id;

        return [
            // pessoa

            'name' => [
                'required',
                'string',
                'max:255'
            ],

            'document' => [
                'required',
                'string',
                Rule::unique('people', 'document')->ignore($personId),
            ],

            'birth_date' => [
                'required',
                'date'
            ],

            'gender' => [
                'nullable',
                'in:male,female,other,not_specified'
            ],

            'email' => [
                'required',
                'email'
            ],

            'phone' => [
                'nullable',
                'string'
            ],

            'address' => [
                'nullable',
                'string'
            ],

            // aluno

            'registration' => [
                'required',
                'string',
                Rule::unique('students', 'registration')->ignore($studentId),
            ],

            'entry_date' => [
                'required',
                'date'
            ],

            'status' => [
                'sometimes',
                'in:active,locked,completed,dropped'
            ],
        ];
    }
}
