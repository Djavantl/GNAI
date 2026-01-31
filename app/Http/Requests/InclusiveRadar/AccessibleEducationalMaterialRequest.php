<?php

namespace App\Http\Requests\InclusiveRadar;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\InclusiveRadar\ResourceType;

class AccessibleEducationalMaterialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $material = $this->route('material');

        $isDigital = false;
        if ($this->type_id) {
            $isDigital = ResourceType::where('id', $this->type_id)->where('is_digital', true)->exists();
        }

        return [
            'title' => 'required|string|max:255',
            'type_id' => 'required|exists:resource_types,id',
            'asset_code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('accessible_educational_materials', 'asset_code')->ignore($material?->id),
            ],

            'quantity' => $isDigital
                ? 'nullable'
                : 'required|integer|min:0',

            'requires_training' => 'sometimes|boolean',
            'is_active' => 'sometimes|boolean',
            'status_id' => 'nullable|exists:resource_statuses,id',
            'notes' => 'nullable|string',

            'deficiencies' => 'required|array|min:1',
            'deficiencies.*' => 'exists:deficiencies,id',

            'accessibility_features' => 'nullable|array',
            'accessibility_features.*' => 'exists:accessibility_features,id',

            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',

            'attributes' => 'nullable|array',
            'attributes.*' => 'nullable',
        ];
    }

    protected function prepareForValidation()
    {
        $isDigital = ResourceType::where('id', $this->type_id)->where('is_digital', true)->exists();

        $this->merge([
            'requires_training' => $this->boolean('requires_training'),
            'is_active' => $this->boolean('is_active'),
            'quantity' => $isDigital ? null : $this->quantity,
        ]);
    }

    public function messages(): array
    {
        return [
            'title.required' => 'O título do material pedagógico é obrigatório.',
            'type_id.required' => 'Selecione uma categoria/tipo de material.',
            'quantity.required' => 'Para materiais físicos, a quantidade é obrigatória.',
            'quantity.integer' => 'A quantidade deve ser um número inteiro.',
            'asset_code.unique' => 'O código do ativo (patrimônio) já está em uso.',
            'deficiencies.required' => 'Selecione pelo menos uma deficiência atendida.',
            'images.*.max' => 'Cada imagem não pode ser maior que 2MB.',
            'accessibility_features.*.exists' => 'Um dos recursos de acessibilidade selecionados é inválido.',
        ];
    }
}
