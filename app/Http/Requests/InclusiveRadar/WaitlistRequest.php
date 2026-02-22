<?php

namespace App\Http\Requests\InclusiveRadar;

use App\Models\InclusiveRadar\AccessibleEducationalMaterial;
use App\Models\InclusiveRadar\AssistiveTechnology;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WaitlistRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [];

        if ($this->isMethod('POST')) {
            $rules = [
                'waitlistable_id' => ['required','integer'],
                'waitlistable_type' => [
                    'required','string',
                    Rule::in([AssistiveTechnology::class, AccessibleEducationalMaterial::class]),
                ],
                'student_id' => 'nullable|exists:students,id',
                'professional_id' => 'nullable|exists:professionals,id',
                'user_id' => 'required|exists:users,id',
                'observation' => ['nullable','string'],
            ];
        }

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules = [
                'status' => [
                    'nullable',
                    Rule::in(['waiting','notified','fulfilled','cancelled']),
                ],
                'observation' => ['nullable','string'],
            ];
        }

        return $rules;
    }

    protected function prepareForValidation(): void
    {
        if (!$this->has('user_id') && auth()->check()) {
            $this->merge(['user_id' => auth()->id()]);
        }
    }

    public function withValidator($validator)
    {
        if ($this->isMethod('POST')) {
            $validator->after(function ($validator) {
                $student = $this->input('student_id');
                $professional = $this->input('professional_id');

                if (empty($student) && empty($professional)) {
                    $validator->errors()->add('student_id', 'É necessário informar um aluno ou um profissional.');
                }

                if (!empty($student) && !empty($professional)) {
                    $validator->errors()->add('student_id', 'Não é permitido informar aluno e profissional ao mesmo tempo.');
                }
            });
        }
    }

    public function messages(): array
    {
        return [
            'waitlistable_id.required' => 'O recurso é obrigatório.',
            'waitlistable_type.required' => 'O tipo de recurso é obrigatório.',
            'waitlistable_type.in' => 'Tipo de recurso inválido.',
            'student_id.exists' => 'Aluno inválido.',
            'professional_id.exists' => 'Profissional inválido.',
            'user_id.required' => 'O usuário responsável é obrigatório.',
            'status.in' => 'Status inválido.',
        ];
    }
}
