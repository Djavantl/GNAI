<?php

namespace App\Http\Requests\SpecializedEducationalSupport;

use Illuminate\Foundation\Http\FormRequest;

class MethodologyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'pei_id' => ['required', 'exists:peis,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'resources_used' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'pei_id.required' => 'O PEI é obrigatório.',
            'pei_id.exists' => 'O PEI informado é inválido.',
            'title.required' => 'O título da metodologia é obrigatório.',
            'title.string' => 'O título da metodologia deve ser um texto válido.',
            'title.max' => 'O título da metodologia não pode ultrapassar 255 caracteres.',
            'description.required' => 'A descrição da metodologia é obrigatória.',
            'description.string' => 'A descrição da metodologia deve ser um texto válido.',
            'resources_used.string' => 'Os recursos utilizados devem ser informados em um texto válido.',
        ];
    }
}
