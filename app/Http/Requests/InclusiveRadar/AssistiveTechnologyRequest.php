<?php

namespace App\Http\Requests\InclusiveRadar;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use App\Enums\InclusiveRadar\InspectionType;
use App\Enums\InclusiveRadar\ConservationState;

class AssistiveTechnologyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tech = $this->route('assistiveTechnology');
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');

        $isDigital = $this->boolean('is_digital');

        return [

            'name' => 'required|string|max:255',

            'is_digital' => 'required|boolean',

            'notes' => 'nullable|string',

            'asset_code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('assistive_technologies', 'asset_code')
                    ->ignore($tech?->id),
            ],

            'quantity' => $isDigital
                ? 'nullable|integer|min:0'
                : 'required|integer|min:1',

            'quantity_available' => 'nullable|integer|min:0',

            'is_active' => 'sometimes|boolean',
            'deficiencies' => 'required|array|min:1',
            'deficiencies.*' => 'exists:deficiencies,id',

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
            'name.required' => 'Informe o tipo da tecnologia assistiva.',
            'quantity.required' => 'Para recursos físicos, a quantidade é obrigatória.',
            'asset_code.unique' => 'O código patrimonial já está em uso.',
            'deficiencies.required' => 'Selecione pelo menos um público-alvo.',
            'conservation_state.required' => 'O estado de conservação atual é obrigatório no cadastro.',
            'inspection_date.before_or_equal' => 'A data da inspeção não pode ser no futuro.',
            'images.*.image' => 'O arquivo deve ser uma imagem.',
            'images.*.max' => 'Cada imagem não pode ser maior que 2MB.',
        ];
    }
}
