<?php

namespace App\Http\Requests\InclusiveRadar;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AccessibilityFeatureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $feature = $this->route('accessibilityFeature');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('accessibility_features')->ignore($feature?->id),
            ],
            'description' => 'nullable|string|max:1000',
            'is_active' => 'sometimes|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome do recurso é obrigatório.',
            'name.unique' => 'Esse recurso já existe.',
            'description.string' => 'A descrição deve ser um texto.',
            'is_active.boolean' => 'O status deve ser verdadeiro ou falso.',
        ];
    }
}
