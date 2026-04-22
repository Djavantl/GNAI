<?php

namespace App\Http\Requests\SpecializedEducationalSupport;

use Illuminate\Foundation\Http\FormRequest;

class DisciplineRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome da disciplina é obrigatório.',
            'name.string' => 'O nome da disciplina deve ser um texto válido.',
            'name.max' => 'O nome da disciplina não pode ultrapassar 255 caracteres.',
            'description.string' => 'A descrição da disciplina deve ser um texto válido.',
            'is_active.boolean' => 'O campo de situação da disciplina é inválido.',
        ];
    }
}
