<?php

namespace App\Http\Requests\SpecializedEducationalSupport;
use App\Enums\Priority;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class PendencyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'assigned_to' => [
                'required',
                'integer',
                'exists:professionals,id',
            ],

            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'priority' => [ new Enum(Priority::class) ],

            'due_date' => [
                'nullable',
                'date',
                'after_or_equal:today',
            ],

            'is_completed' => [
                'sometimes',
                'boolean',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'assigned_to.required' => 'O profissional responsável é obrigatório.',
            'assigned_to.integer' => 'O profissional responsável informado é inválido.',
            'assigned_to.exists'   => 'O profissional responsável selecionado é inválido.',
            'title.required' => 'O título da pendência é obrigatório.',
            'title.string' => 'O título da pendência deve ser um texto válido.',
            'title.max'      => 'O título da pendência não pode ultrapassar 255 caracteres.',
            'description.string' => 'A descrição da pendência deve ser um texto válido.',
            'priority.enum' => 'A prioridade selecionada é inválida.',
            'due_date.date' => 'A data limite deve ser uma data válida.',
            'due_date.after_or_equal' => 'A data limite deve ser hoje ou uma data futura.',
            'is_completed.boolean' => 'O campo de conclusão da pendência é inválido.',
        ];
    }

}
