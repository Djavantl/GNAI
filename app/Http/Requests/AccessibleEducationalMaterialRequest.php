<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AccessibleEducationalMaterialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $material = $this->route('accessibleEducationalMaterial');

        return [
            'title' => 'required|string|max:255',
            'type' => 'nullable|string|max:255',
            'format' => 'nullable|string|max:255',
            'language' => 'nullable|string|max:10',
            'isbn' => [
                'nullable',
                'string',
                Rule::unique('accessible_educational_materials', 'isbn')->ignore($material?->id),
            ],
            'publisher' => 'nullable|string|max:255',
            'edition' => 'nullable|string|max:255',
            'publication_date' => 'nullable|date',
            'pages' => 'nullable|integer|min:1',
            'asset_code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('accessible_educational_materials', 'asset_code')->ignore($material?->id),
            ],
            'location' => 'nullable|string|max:255',
            'conservation_state' => 'nullable|string|max:50',
            'cost' => 'nullable|numeric|min:0',
            'requires_training' => 'sometimes|boolean',
            'is_active' => 'sometimes|boolean',
            'accessible_educational_material_status_id' => 'nullable|exists:accessible_educational_material_statuses,id',
            'deficiencies' => 'required|array|min:1',
            'deficiencies.*' => 'exists:deficiencies,id',
            'accessibility_features' => 'nullable|array',
            'accessibility_features.*' => 'exists:accessibility_features,id',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'requires_training' => $this->has('requires_training'),
            'is_active' => $this->has('is_active'),
            'cost' => $this->cost ? str_replace(['.', ','], ['', '.'], $this->cost) : null,
        ]);
    }

    public function messages(): array
    {
        return [
            'title.required' => 'O título do material pedagógico é obrigatório.',
            'title.max' => 'O título não pode ter mais de 255 caracteres.',
            'isbn.unique' => 'Este ISBN já está cadastrado no sistema.',
            'asset_code.unique' => 'O código do ativo (patrimônio) já está em uso.',
            'publication_date.date' => 'Informe uma data de publicação válida.',
            'pages.integer' => 'O número de páginas deve ser um número inteiro.',
            'pages.min' => 'O material deve ter pelo menos 1 página.',
            'cost.numeric' => 'O valor do custo deve ser um número válido.',
            'accessible_educational_material_status_id.exists' => 'O status selecionado é inválido.',
            'deficiencies.required' => 'Selecione pelo menos uma deficiência atendida por este material.',
            'deficiencies.*.exists' => 'Uma das deficiências selecionadas não existe no sistema.',
            'accessibility_features.*.exists' => 'Um dos recursos de acessibilidade selecionados é inválido.',
            'requires_training.boolean' => 'O campo de necessidade de treinamento deve ser verdadeiro ou falso.',
            'is_active.boolean' => 'O campo ativo deve ser verdadeiro ou falso.',
            'images.*.image' => 'O arquivo enviado deve ser uma imagem.',
            'images.*.mimes' => 'As imagens devem estar nos formatos: jpeg, png, jpg ou webp.',
            'images.*.max' => 'Cada imagem não pode ser maior que 2MB (2048 KB).',
        ];
    }
}
