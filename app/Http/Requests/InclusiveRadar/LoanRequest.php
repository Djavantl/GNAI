<?php

namespace App\Http\Requests\InclusiveRadar;

use App\Enums\InclusiveRadar\LoanStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Validation\Rules\Enum;

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
                new Enum(LoanStatus::class),
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
            'due_date.required' => 'A data de previsão de entrega é obrigatória.',
            'due_date.after_or_equal' => 'A data de entrega não pode ser anterior à data de empréstimo.',
            'return_date.after_or_equal' => 'A data real de devolução deve ser igual ou posterior à data do empréstimo.',
            'status.enum' => 'O status selecionado é inválido.',
        ];
    }

}
