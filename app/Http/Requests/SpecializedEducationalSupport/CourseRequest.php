<?php

namespace App\Http\Requests\SpecializedEducationalSupport;

use Illuminate\Foundation\Http\FormRequest;

class CourseRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'discipline_ids' => 'nullable|array',
            'discipline_ids.*' => 'exists:disciplines,id',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'is_active' => filter_var($this->is_active, FILTER_VALIDATE_BOOLEAN),
        ]);
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome do curso é obrigatório.',
            'name.string' => 'O nome do curso deve ser um texto válido.',
            'name.max' => 'O nome do curso não pode ultrapassar 255 caracteres.',
            'description.string' => 'A descrição do curso deve ser um texto válido.',
            'is_active.boolean' => 'O campo de situação do curso é inválido.',
            'discipline_ids.array' => 'As disciplinas do curso devem ser informadas em uma lista válida.',
            'discipline_ids.*.exists' => 'Uma das disciplinas selecionadas é inválida.',
        ];
    }
}
