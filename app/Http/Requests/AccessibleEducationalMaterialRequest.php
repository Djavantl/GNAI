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
            'type' => 'nullable|string|max:100',
            'format' => 'nullable|string|max:100',
            'language' => 'nullable|string|max:10',
            'isbn' => [
                'nullable',
                'string',
                'max:30',
                Rule::unique('accessible_educational_materials', 'isbn')
                    ->ignore($material?->id),
            ],
            'publisher' => 'nullable|string|max:255',
            'edition' => 'nullable|string|max:50',
            'publication_date' => 'nullable|date',
            'pages' => 'nullable|integer|min:1',
            'accessibilities' => 'nullable|array',
            'accessibilities.*' => 'exists:accessibility_features,id',

            'asset_code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('accessible_educational_materials', 'asset_code')
                    ->ignore($material?->id),
            ],

            'location' => 'nullable|string|max:255',
            'conservation_state' => 'nullable|string|max:50',
            'requires_training' => 'sometimes|boolean',
            'cost' => 'nullable|numeric|min:0',

            'accessible_educational_material_status_id' =>
                'nullable|exists:accessible_educational_material_statuses,id',

            'is_active' => 'sometimes|boolean',
            'deficiencies' => 'required|array',
            'deficiencies.*' => 'exists:deficiencies,id',
        ];
    }


    public function messages(): array
    {
        return [
            'title.required' => 'O título do material é obrigatório.',
            'asset_code.unique' => 'O código patrimonial já está em uso.',
            'pages.integer' => 'O número de páginas deve ser um valor inteiro.',
            'cost.numeric' => 'O custo deve ser um valor numérico.',
            'requires_training.boolean' => 'Informe verdadeiro ou falso para necessidade de treinamento.',
            'is_active.boolean' => 'O campo ativo deve ser verdadeiro ou falso.',
            'accessible_educational_material_status_id.exists' => 'O status selecionado é inválido.',
            'deficiencies.required' => 'Selecione pelo menos uma deficiência.',
            'deficiencies.*.exists' => 'Uma das deficiências selecionadas é inválida.',
            'accessibilities.array' => 'Selecione as acessibilidades como uma lista.',
            'accessibilities.*.exists' => 'Uma das acessibilidades selecionadas é inválida.',
        ];
    }
}
