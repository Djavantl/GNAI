<?php

namespace App\Http\Requests\InclusiveRadar;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use App\Enums\InclusiveRadar\InspectionType;
use App\Enums\InclusiveRadar\ConservationState;
use App\Enums\InclusiveRadar\ResourceStatus;

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
        $isDigital = $this->boolean('is_digital');

        return [

            'name' => 'required|string|max:255',

            'is_digital' => 'required|boolean',

            'is_loanable' => 'sometimes|boolean',

            'asset_code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('accessible_educational_materials', 'asset_code')
                    ->ignore($material?->id),
            ],

            /* Materiais digitais não possuem estoque físico limitado, por isso a
               quantidade torna-se opcional, diferentemente de itens físicos. */
            'quantity' => $isDigital
                ? 'nullable|integer|min:0'
                : 'required|integer|min:0',

            'quantity_available' => 'nullable|integer|min:0',

            'is_active' => 'sometimes|boolean',

            'notes' => 'nullable|string',

            'status' => [
                $isUpdate ? 'nullable' : 'required',
                new Enum(ResourceStatus::class),
            ],

            'deficiencies' => 'required|array|min:1',
            'deficiencies.*' => 'exists:deficiencies,id',

            'accessibility_features' => 'nullable|array',
            'accessibility_features.*' => 'exists:accessibility_features,id',

            /* Na criação, o status e estado de conservação são obrigatórios para
               definir o ponto de partida do material no acervo. */
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
        ];
    }

    protected function prepareForValidation(): void
    {
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');

        /* Normalizamos os campos booleanos e garantimos que a data de inspeção
           seja preenchida com o dia atual caso o usuário omita o campo. */
        $this->merge([
            'is_active' => $this->boolean('is_active'),
            'is_digital' => $this->boolean('is_digital'),
            'is_loanable' => $this->boolean('is_loanable'),
            'inspection_date' => $this->inspection_date ?? now()->format('Y-m-d'),
        ]);

        if (!$isUpdate) {
            /* Todo material novo entra no sistema através de uma inspeção do tipo inicial. */
            $this->merge([
                'inspection_type' => $this->inspection_type ?? InspectionType::INITIAL->value,
            ]);
        }
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome do material pedagógico é obrigatório.',
            'quantity.required' => 'Para materiais físicos, a quantidade é obrigatória.',
            'asset_code.unique' => 'O código patrimonial já está em uso.',
            'deficiencies.required' => 'Selecione pelo menos um público-alvo.',
            'conservation_state.required' => 'O estado de conservação atual é obrigatório.',
            'inspection_date.before_or_equal' => 'A data da inspeção não pode ser no futuro.',
            'images.*.image' => 'O arquivo deve ser uma imagem.',
            'images.*.max' => 'Cada imagem não pode ser maior que 2MB.',
            'accessibility_features.*.exists' => 'Um dos recursos de acessibilidade selecionados é inválido.',
        ];
    }
}
