<?php

namespace App\Http\Requests\InclusiveRadar;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InstitutionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $institution = $this->route('institution');

        return [
            'name' => 'required|string|max:255',
            'short_name' => 'nullable|string|max:100',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'district' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'default_zoom' => 'nullable|integer|min:0',
            'is_active' => 'sometimes|boolean',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'is_active' => $this->has('is_active'),
        ]);
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome da instituição é obrigatório.',
            'name.max' => 'O nome da instituição não pode ter mais de 255 caracteres.',
            'city.required' => 'A cidade é obrigatória.',
            'city.max' => 'O nome da cidade não pode ter mais de 255 caracteres.',
            'state.required' => 'O estado é obrigatório.',
            'state.size' => 'O nome do estado não pode ter mais de 255 caracteres.',
            'district'=> 'O nome do distrito não pode ter mais de 255 caracteres.',
            'address'=> 'O nome do endereço/rua não pode ter mais de 255 caracteres.',
            'latitude.required' => 'A latitude é obrigatória.',
            'latitude.numeric' => 'A latitude deve ser um número válido.',
            'latitude.between' => 'A latitude deve estar entre -90 e 90.',
            'longitude.required' => 'A longitude é obrigatória.',
            'longitude.numeric' => 'A longitude deve ser um número válido.',
            'longitude.between' => 'A longitude deve estar entre -180 e 180.',
            'default_zoom.integer' => 'O zoom padrão deve ser um número inteiro.',
            'default_zoom.min' => 'O zoom padrão não pode ser negativo.',
            'is_active.boolean' => 'O campo ativo deve ser verdadeiro ou falso.',
        ];
    }
}
