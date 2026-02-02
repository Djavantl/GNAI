<?php

namespace App\Http\Requests\InclusiveRadar;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Relations\Relation;

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
                Rule::in(array_values(Relation::morphMap())),
            ],

            'student_id' => 'required_without:professional_id|nullable|exists:students,id',
            'professional_id' => 'required_without:student_id|nullable|exists:professionals,id',

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

    protected function prepareForValidation(): void
    {

        if ($this->has('loanable_type')) {
            $modelClass = Relation::getMorphedModel($this->loanable_type);

            if ($modelClass) {
                $this->merge([
                    'loanable_type' => $modelClass
                ]);
            }
        }

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
            'student_id.required_without' => 'É necessário selecionar um estudante ou um profissional.',
            'student_id.exists' => 'O estudante selecionado não foi encontrado no sistema.',
            'professional_id.required_without' => 'O profissional responsável deve ser identificado.',
            'due_date.required' => 'A data de previsão de entrega é obrigatória.',
            'due_date.after_or_equal' => 'A data de entrega não pode ser anterior à data de empréstimo.',
            'status.in' => 'O status selecionado é inválido.',
        ];
    }
}
