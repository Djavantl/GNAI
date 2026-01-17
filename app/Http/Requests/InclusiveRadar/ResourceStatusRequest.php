<?php

namespace App\Http\Requests\InclusiveRadar;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ResourceStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:100',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'blocks_loan' => [
                'sometimes',
                'boolean',
            ],

            'blocks_access' => [
                'sometimes',
                'boolean',
            ],

            'for_assistive_technology' => [
                'sometimes',
                'boolean',
            ],

            'for_educational_material' => [
                'sometimes',
                'boolean',
            ],

            'is_active' => [
                'sometimes',
                'boolean',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'O código do status é obrigatório.',
            'code.unique' => 'Já existe um status com este código.',
            'code.max' => 'O código pode ter no máximo 50 caracteres.',

            'name.required' => 'O nome do status é obrigatório.',
            'name.max' => 'O nome pode ter no máximo 100 caracteres.',

            'blocks_loan.boolean' => 'O campo "bloqueia empréstimo" deve ser verdadeiro ou falso.',
            'blocks_access.boolean' => 'O campo "bloqueia acesso" deve ser verdadeiro ou falso.',
        ];
    }
}
