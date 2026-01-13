<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssistiveTechnologyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tech = $this->route('assistiveTechnology');

        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'nullable|string|max:100',
            'quantity' => 'nullable|integer|min:0',
            'asset_code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('assistive_technologies', 'asset_code')->ignore($tech?->id),
            ],
            'conservation_state' => 'nullable|string|max:50',
            'requires_training' => 'sometimes|boolean',
            'is_active' => 'sometimes|boolean',
            'assistive_technology_status_id' => 'nullable|exists:assistive_technology_statuses,id',
            'deficiencies' => 'required|array|min:1',
            'deficiencies.*' => 'exists:deficiencies,id',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'requires_training' => $this->has('requires_training'),
            'is_active' => $this->has('is_active'),
        ]);
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome da tecnologia assistiva é obrigatório.',
            'asset_code.unique' => 'O código do ativo já está em uso.',
            'quantity.integer' => 'A quantidade deve ser um número inteiro.',
            'requires_training.boolean' => 'O campo de necessidade de treinamento deve ser verdadeiro ou falso.',
            'is_active.boolean' => 'O campo ativo deve ser verdadeiro ou falso.',
            'assistive_technology_status_id.exists' => 'O status selecionado é inválido.',
            'deficiencies.required' => 'Selecione pelo menos uma deficiência.',
            'deficiencies.*.exists' => 'Uma das deficiências selecionadas é inválida.',
            'images.*.image' => 'O arquivo deve ser uma imagem.',
            'images.*.mimes' => 'A imagem deve ser do tipo: jpeg, png, jpg ou webp.',
            'images.*.max' => 'Cada imagem não pode ser maior que 2MB.',
        ];
    }
}
