<?php

namespace App\Http\Requests\InclusiveRadar;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'institution_id'  => 'required|exists:institutions,id',
            'name'            => 'required|string|max:255',
            'type'            => 'nullable|string|max:100',
            'description'     => 'nullable|string',
            'latitude'        => 'required|numeric|between:-90,90',
            'longitude'       => 'required|numeric|between:-180,180',
            'google_place_id' => 'nullable|string|max:255',
            'is_active'       => 'boolean',
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
            'institution_id.required' => 'A instituição é obrigatória.',
            'institution_id.exists' => 'A instituição selecionada não existe.',
            'name.required' => 'O nome do local é obrigatório.',
            'name.max' => 'O nome do local não pode ter mais de 255 caracteres.',
            'type.max' => 'O tipo do local não pode ter mais de 100 caracteres.',
            'latitude.numeric' => 'A latitude deve ser um número válido.',
            'latitude.between' => 'A latitude deve estar entre -90 e 90.',
            'longitude.numeric' => 'A longitude deve ser um número válido.',
            'longitude.between' => 'A longitude deve estar entre -180 e 180.',
            'google_place_id.max' => 'O ID do Google Place não pode ter mais de 255 caracteres.',
            'is_active.boolean' => 'O campo ativo deve ser verdadeiro ou falso.',
        ];
    }
}
