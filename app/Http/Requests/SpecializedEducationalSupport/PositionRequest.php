<?php

namespace App\Http\Requests\SpecializedEducationalSupport;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PositionRequest extends FormRequest
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
        $id = $this->route('position') ? $this->route('position')->id ?? $this->route('position') : null;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome do cargo é obrigatório.',
            'name.string' => 'O nome do cargo deve ser um texto válido.',
            'name.max' => 'O nome não pode ultrapassar 255 caracteres.',
            'description.string' => 'A descrição do cargo deve ser um texto válido.',
            'permissions.array' => 'As permissões devem ser informadas em uma lista válida.',
            'permissions.*.exists' => 'Uma das permissões selecionadas é inválida.',
        ];
    }
}
