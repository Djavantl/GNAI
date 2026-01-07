<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AccessibleEducationalMaterialStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $status = $this->route('status');

        return [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('accessible_educational_material_statuses', 'name')
                    ->ignore($status instanceof \App\Models\AccessibleEducationalMaterialStatus ? $status->id : $status),
            ],
            'description' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome do status é obrigatório.',
            'name.unique' => 'Já existe um status com esse nome.',
            'is_active.boolean' => 'O campo ativo deve ser verdadeiro ou falso.',
        ];
    }
}
