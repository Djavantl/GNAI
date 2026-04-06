<?php

namespace App\Http\Requests\InclusiveRadar;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use App\Enums\InclusiveRadar\InspectionType;
use App\Enums\InclusiveRadar\ConservationState;
use App\Enums\InclusiveRadar\ResourceStatus;

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
            'is_loanable' => 'sometimes|boolean',
            'notes' => 'nullable|string',

            'asset_code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('assistive_technologies', 'asset_code')
                    ->ignore($tech?->id),
            ],

            /* Tecnologias puramente digitais (softwares) não possuem controle de
               estoque físico, tornando a quantidade um campo opcional. */
            'quantity' => $isDigital ? 'nullable|integer' : 'required|integer',

            'quantity_available' => 'nullable|integer|min:0',
            'is_active' => 'sometimes|boolean',

            /* No cadastro inicial, é obrigatório definir o estado operacional e
               de conservação para garantir o rastreio desde a entrada no acervo. */
            'status' => [
                $isUpdate ? 'nullable' : 'required',
                new Enum(ResourceStatus::class),
            ],

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

        /* Garantimos a integridade dos tipos booleanos e definimos a data atual
           como padrão de inspeção caso nenhuma seja fornecida pelo usuário. */
        $this->merge([
            'is_active' => $this->boolean('is_active'),
            'is_digital' => $this->boolean('is_digital'),
            'is_loanable' => $this->boolean('is_loanable'),
            'inspection_date' => $this->inspection_date ?? now()->format('Y-m-d'),
        ]);

        if (!$isUpdate) {
            /* Definimos que toda tecnologia assistiva recém-cadastrada inicia
               obrigatoriamente com o registro de uma Inspeção Inicial. */
            $this->merge([
                'inspection_type' => $this->inspection_type ?? InspectionType::INITIAL->value,
            ]);
        }
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Informe o tipo da tecnologia assistiva.',
            'quantity.required' => 'A quantidade é obrigatória para este recurso.',
            'asset_code.unique' => 'O código patrimonial já está em uso.',
            'deficiencies.required' => 'Selecione pelo menos um público-alvo.',
            'conservation_state.required' => 'O estado de conservação atual é obrigatório.',
            'inspection_date.before_or_equal' => 'A data da inspeção não pode ser no futuro.',
            'images.*.image' => 'O arquivo deve ser uma imagem.',
            'images.*.max' => 'Cada imagem não pode ser maior que 2MB.',
        ];
    }
}
