<?php

namespace App\Http\Requests\SpecializedEducationalSupport;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DeficiencyRequest extends FormRequest
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
        $id = $this->route('deficiency') ? $this->route('deficiency')->id ?? $this->route('deficiency') : null;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'cid_code' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('deficiencies', 'cid_code')->ignore($id),
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'is_active' => [
                'sometimes',
                'boolean',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome da deficiência é obrigatório.',
            'name.max' => 'O nome não pode ultrapassar 255 caracteres.',
            'cid_code.max' => 'O código CID deve ter no máximo 20 caracteres.',
            'is_active.boolean' => 'O campo ativo deve ser verdadeiro ou falso.',
        ];
    }
}
