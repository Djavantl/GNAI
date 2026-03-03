<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;
use App\Rules\Cpf;


class ProfileRequest extends FormRequest
{
    /**
     * Determina se o usuário tem permissão para esta requisição.
     */
    public function authorize(): bool
    {
        // Apenas profissionais e professores podem acessar essa rota
        return auth()->check() && (auth()->user()->professional || auth()->user()->teacher);
    }

    /**
     * Regras de validação.
     */
    public function rules(): array
    {
        $user = auth()->user();
        
        // Descobre o ID da 'Person' vinculada ao usuário logado
        $personId = $user->professional?->person_id ?? $user->teacher?->person_id;

        return [
            // Dados Pessoais (Tabela People)
            'name' => ['required', 'string', 'max:255'],
            'document' => [
                'required',
                'string',
                new Cpf, 
                Rule::unique('people', 'document')->ignore($personId),
            ],
            'birth_date' => ['required', 'date', 'before:today'],
            'gender' => ['required', Rule::in(['male', 'female', 'other', 'not_specified'])],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => [
                'required', 
                'email', 
                Rule::unique('people', 'email')->ignore($personId)
            ],
            'address' => ['nullable', 'string', 'max:500'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'remove_photo' => [
                'nullable',
                'boolean' 
            ],

            // Segurança (Tabela Users)
            'password' => [
                'nullable', 
                'confirmed', 
                Password::min(8)->letters()->numbers()
            ],
        ];
    }

    /**
     * Customização de mensagens (opcional, mas melhora a UX)
     */
    public function messages(): array
    {
        return [
            'document.unique' => 'Este CPF/Documento já está em uso.',
            'email.unique' => 'Este e-mail já está cadastrado em outra conta.',
            'password.confirmed' => 'As senhas digitadas não conferem.',
            'password.min' => 'A nova senha deve ter pelo menos 8 caracteres.',
            'photo.max' => 'A foto não pode ser maior que 2MB.',
        ];
    }
}