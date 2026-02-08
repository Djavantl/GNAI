<?php

namespace App\Http\Requests\InclusiveRadar;

use Illuminate\Foundation\Http\FormRequest;

class ResourceStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],

            'blocks_loan' => ['boolean'],
            'blocks_access' => ['boolean'],
            'for_assistive_technology' => ['boolean'],
            'for_educational_material' => ['boolean'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome do status é obrigatório.',
            'name.max' => 'O nome pode ter no máximo 100 caracteres.',

            'blocks_loan.boolean' => 'O campo "bloqueia empréstimo" deve ser verdadeiro ou falso.',
            'blocks_access.boolean' => 'O campo "bloqueia acesso" deve ser verdadeiro ou falso.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'blocks_loan' => $this->boolean('blocks_loan'),
            'blocks_access' => $this->boolean('blocks_access'),
            'for_assistive_technology' => $this->boolean('for_assistive_technology'),
            'for_educational_material' => $this->boolean('for_educational_material'),
            'is_active' => $this->boolean('is_active'),
        ]);
    }
}
