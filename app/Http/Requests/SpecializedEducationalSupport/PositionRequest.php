<?php

namespace App\Http\Requests\SpecializedEducationalSupport;

use Illuminate\Foundation\Http\FormRequest;

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

            'is_active' => [
                'sometimes',
                'boolean',
            ],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome do cargo é obrigatório.',
            'name.max' => 'O nome não pode ultrapassar 255 caracteres.',
            'cid_code.max' => 'O código CID deve ter no máximo 20 caracteres.',
            'is_active.boolean' => 'O campo ativo deve ser verdadeiro ou falso.',
        ];
    }
}
