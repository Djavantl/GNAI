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
        $isLoanable = $this->boolean('is_loanable');

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

            'quantity' => $isDigital
                ? 'nullable|integer|min:0'
                : 'required|integer|min:1',

            'quantity_available' => $isLoanable
                ? 'nullable|integer|min:0'
                : 'nullable',

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

        $this->merge([
            'is_active' => $this->boolean('is_active'),
            'is_digital' => $this->boolean('is_digital'),
            'is_loanable' => $this->boolean('is_loanable'),
            'inspection_date' => $this->inspection_date ?? now()->format('Y-m-d'),
        ]);

        if (!$this->boolean('is_loanable')) {
            $this->merge([
                'quantity_available' => 0,
            ]);
        }

        if (!$isUpdate) {
            $this->merge([
                'inspection_type' => $this->inspection_type ?? InspectionType::INITIAL->value,
            ]);
        }
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            if ($this->boolean('is_loanable') && (int)$this->quantity <= 0) {
                $validator->errors()->add(
                    'quantity',
                    'Materiais emprestáveis devem ter quantidade maior que zero.'
                );
            }

            if (
                $this->boolean('is_loanable') &&
                $this->quantity !== null &&
                $this->quantity_available !== null &&
                $this->quantity_available > $this->quantity
            ) {
                $validator->errors()->add(
                    'quantity_available',
                    'A quantidade disponível não pode ser maior que a quantidade total.'
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome do material pedagógico é obrigatório.',
            'is_digital.required' => 'Informe se o material é digital ou físico.',
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
