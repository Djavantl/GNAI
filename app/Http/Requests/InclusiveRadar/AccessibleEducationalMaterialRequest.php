<?php

namespace App\Http\Requests\InclusiveRadar;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use App\Models\InclusiveRadar\ResourceType;
use App\Enums\InclusiveRadar\InspectionType;
use App\Enums\InclusiveRadar\ConservationState;

class AccessibleEducationalMaterialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $material = $this->route('material');
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');

        $isDigital = false;
        if ($this->type_id) {
            $isDigital = ResourceType::where('id', $this->type_id)
                ->where('is_digital', true)
                ->exists();
        }

        return [
            'name' => 'required|string|max:255',
            'type_id' => 'required|exists:resource_types,id',
            'asset_code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('accessible_educational_materials', 'asset_code')
                    ->ignore($material?->id),
            ],
            'quantity' => $isDigital ? 'nullable' : 'required|integer|min:0',

            'is_active' => 'sometimes|boolean',
            'notes' => 'nullable|string',
            'deficiencies' => 'required|array|min:1',
            'deficiencies.*' => 'exists:deficiencies,id',

            'accessibility_features' => 'nullable|array',
            'accessibility_features.*' => 'exists:accessibility_features,id',

            'conservation_state' => [
                $isUpdate ? 'nullable' : 'required',
                new Enum(ConservationState::class),
            ],
            'inspection_type' => [
                $isUpdate ? 'nullable' : 'required',
                new Enum(InspectionType::class),
            ],
            'inspection_date' => [
                'required',
                'date',
                'before_or_equal:today',
            ],
            'inspection_description' => 'nullable|string|max:1000',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
            'attributes' => 'nullable|array',
        ];
    }

    protected function prepareForValidation(): void
    {
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');

        $this->merge([
            'is_active' => $this->boolean('is_active'),
            'inspection_date' => $this->inspection_date ?? now()->format('Y-m-d'),
        ]);

        if (!$isUpdate) {
            $this->merge([
                'inspection_type' => $this->inspection_type ?? InspectionType::INITIAL->value,
            ]);
        }
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome do material pedagógico é obrigatório.',
            'type_id.required' => 'Selecione uma categoria/tipo de material.',
            'quantity.required' => 'Para materiais físicos, a quantidade é obrigatória.',
            'asset_code.unique' => 'O código patrimonial já está em uso.',
            'deficiencies.required' => 'Selecione pelo menos um público-alvo.',
            'conservation_state.required' => 'O estado de conservação atual é obrigatório no cadastro.',
            'inspection_date.before_or_equal' => 'A data da inspeção não pode ser no futuro.',
            'images.*.image' => 'O arquivo deve ser uma imagem.',
            'images.*.max' => 'Cada imagem não pode ser maior que 2MB.',
            'accessibility_features.*.exists' => 'Um dos recursos de acessibilidade selecionados é inválido.',
        ];
    }
}
