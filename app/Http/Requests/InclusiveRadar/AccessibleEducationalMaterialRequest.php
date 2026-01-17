<?php

namespace App\Http\Requests\InclusiveRadar;

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
        $material = $this->route('material');

        return [
            'title' => 'required|string|max:255',
            'type_id' => 'required|exists:resource_types,id',
            'asset_code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('accessible_educational_materials', 'asset_code')->ignore($material?->id),
            ],
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
        $this->merge([
            'requires_training' => $this->boolean('requires_training'),
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    public function messages(): array
    {
        return [
            'title.required' => 'O título do material pedagógico é obrigatório.',
            'title.max' => 'O título não pode ter mais de 255 caracteres.',
            'type_id.required' => 'Selecione uma categoria/tipo de material.',
            'type_id.exists' => 'A categoria selecionada é inválida.',
            'asset_code.unique' => 'O código do ativo (patrimônio) já está em uso.',
            'deficiencies.required' => 'Selecione pelo menos uma deficiência atendida por este material.',
            'deficiencies.*.exists' => 'Uma das deficiências selecionadas não existe no sistema.',
            'accessibility_features.*.exists' => 'Um dos recursos de acessibilidade selecionados é inválido.',
            'requires_training.boolean' => 'O campo de necessidade de treinamento deve ser verdadeiro ou falso.',
            'is_active.boolean' => 'O campo ativo deve ser verdadeiro ou falso.',
            'images.*.image' => 'O arquivo enviado deve ser uma imagem.',
            'images.*.mimes' => 'As imagens devem estar nos formatos: jpeg, png, jpg ou webp.',
            'images.*.max' => 'Cada imagem não pode ser maior que 2MB.',
            'attributes.array' => 'Os atributos adicionais devem ser enviados como um array.',
        ];
    }
}
