<?php

namespace App\Http\Requests\InclusiveRadar;

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
            'type_id' => 'required|exists:resource_types,id',
            'asset_code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('assistive_technologies', 'asset_code')->ignore($tech?->id),
            ],
            'conservation_state' => 'nullable|string|max:50',
            'requires_training' => 'sometimes|boolean',
            'is_active' => 'sometimes|boolean',
            'status_id' => 'nullable|exists:resource_statuses,id',
            'notes' => 'nullable|string',
            'deficiencies' => 'required|array|min:1',
            'deficiencies.*' => 'exists:deficiencies,id',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
            'attributes' => 'nullable|array',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'requires_training' => $this->boolean('requires_training'),
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome da tecnologia assistiva é obrigatório.',
            'type_id.required' => 'Selecione uma categoria/tipo de tecnologia.',
            'type_id.exists' => 'A categoria selecionada é inválida.',
            'asset_code.unique' => 'O código patrimonial já está em uso.',
            'deficiencies.required' => 'Selecione pelo menos um público-alvo (deficiência).',
            'images.*.max' => 'Cada imagem não pode ser maior que 2MB.',
        ];
    }
}
