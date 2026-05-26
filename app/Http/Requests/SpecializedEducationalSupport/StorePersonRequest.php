<?php

namespace App\Http\Requests\SpecializedEducationalSupport;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePersonRequest extends FormRequest
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
            'name'       => 'required|min:3',
            'document'   => [
                'nullable',
                Rule::unique('people', 'document')->ignore($this->person?->id),
            ],
            'birth_date' => 'required|date',
            'gender'     => 'required|in:male,female,other,not_specified',
            'email'      => 'required|email',
            'phone'      => 'nullable',
            'address'    => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome é obrigatório.',
            'name.min' => 'O nome deve ter ao menos 3 caracteres.',
            'document.unique' => 'Este documento já está cadastrado.',
            'birth_date.required' => 'A data de nascimento é obrigatória.',
            'birth_date.date' => 'A data de nascimento deve ser válida.',
            'gender.required' => 'O gênero é obrigatório.',
            'gender.in' => 'O gênero selecionado é inválido.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'O e-mail deve ser válido.',
            'address.string' => 'O endereço deve ser um texto válido.',
        ];
    }
}
