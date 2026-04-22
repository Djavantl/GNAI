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

        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome do perfil de atendimento é obrigatório.',
            'name.string' => 'O nome do perfil de atendimento deve ser um texto válido.',
            'name.max' => 'O nome não pode ultrapassar 255 caracteres.',
            'cid_code.string' => 'O código CID deve ser um texto válido.',
            'cid_code.unique' => 'O código CID informado já está cadastrado.',
            'cid_code.max' => 'O código CID deve ter no máximo 20 caracteres.',
            'description.string' => 'A descrição deve ser um texto válido.',
        ];
    }
}
