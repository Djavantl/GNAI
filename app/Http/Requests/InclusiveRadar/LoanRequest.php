<?php

namespace App\Http\Requests\InclusiveRadar;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'loanable_id' => 'required|integer',
            'loanable_type' => [
                'required',
                'string',
                Rule::in([
                    'App\Models\InclusiveRadar\AssistiveTechnology',
                    'App\Models\InclusiveRadar\AccessibleEducationalMaterial'
                ]),
            ],
            'student_id' => 'required|exists:students,id',
            'professional_id' => 'required|exists:professionals,id',
            'loan_date' => 'required|date',
            'due_date' => [
                'required',
                'date',
                'after_or_equal:loan_date',
            ],
            'return_date' => 'nullable|date|after_or_equal:loan_date',
            'status' => [
                'sometimes',
                Rule::in(['active', 'returned', 'late', 'damaged']),
            ],
            'observation' => 'nullable|string',
        ];
    }

    protected function prepareForValidation()
    {
        if (!$this->has('loan_date')) {
            $this->merge([
                'loan_date' => now()->toDateTimeString(),
            ]);
        }
    }

    public function messages(): array
    {
        return [
            'loanable_id.required' => 'O item para empréstimo não foi identificado.',
            'loanable_type.in' => 'O tipo de item selecionado é inválido.',
            'student_id.required' => 'É necessário selecionar um estudante.',
            'student_id.exists' => 'O estudante selecionado não foi encontrado no sistema.',
            'professional_id.required' => 'O profissional responsável deve ser identificado.',
            'due_date.required' => 'A data de previsão de entrega é obrigatória.',
            'due_date.after_or_equal' => 'A data de entrega não pode ser anterior à data de empréstimo.',
            'status.in' => 'O status selecionado é inválido.',
        ];
    }
}
