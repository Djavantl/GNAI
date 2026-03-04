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

            /* Validamos o tipo do item contra o Morph Map do projeto para garantir que
               apenas modelos permitidos (como Materiais ou Tecnologias) sejam emprestados. */
            'loanable_type' => [
                'required',
                'string',
                Rule::in(array_values(Relation::morphMap())),
            ],

            'student_id' => 'nullable|exists:students,id',
            'professional_id' => 'nullable|exists:professionals,id',

            'user_id' => 'required|exists:users,id',

            'loan_date' => 'required|date',

            'due_date' => [
                'required',
                'date',
                'after_or_equal:loan_date'
            ],

            'return_date' => [
                'nullable',
                'date',
                'after_or_equal:loan_date'
            ],

            'status' => [
                'sometimes',
                new Enum(LoanStatus::class)
            ],

            'observation' => 'nullable|string',
        ];
    }

    protected function prepareForValidation(): void
    {
        /* Convertemos o "alias" recebido no request para o nome completo da classe
           do Model, facilitando a persistência direta em tabelas polimórficas. */
        if ($this->has('loanable_type')) {
            $modelClass = Relation::getMorphedModel($this->loanable_type);

            if ($modelClass) {
                $this->merge(['loanable_type' => $modelClass]);
            }
        }

        if (!$this->has('loan_date')) {
            $this->merge(['loan_date' => now()->toDateTimeString()]);
        }

        /* Automatizamos o vínculo do empréstimo ao usuário logado para garantir
           a auditoria de quem realizou a operação no sistema. */
        if (!$this->has('user_id') && auth()->check()) {
            $this->merge(['user_id' => auth()->id()]);
        }
    }

    public function messages(): array
    {
        return [
            'loanable_id.required' => 'O item para empréstimo não foi identificado.',
            'loanable_type.in' => 'O tipo de item selecionado é inválido.',
            'due_date.required' => 'A data de previsão de entrega é obrigatória.',
            'due_date.after_or_equal' => 'A data de entrega não pode ser anterior à data do empréstimo.',
            'return_date.after_or_equal' => 'A data real de devolução deve ser igual ou posterior à data do empréstimo.',
            'status.enum' => 'O status selecionado é inválido.',
            'user_id.required' => 'O usuário autenticado é obrigatório.',
        ];
    }
}
