<?php

namespace App\Http\Requests\SpecializedEducationalSupport;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\Cpf;

class TeacherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Recupera o model da rota (ex: teachers/{teacher})
        $teacher = $this->route('teacher');
        $personId = $teacher?->person_id;

        return [
            // --- Dados da Pessoa (herdado de Person) ---
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'document' => [
                'required',
                'string',
                new Cpf, // Adiciona a validação real
                Rule::unique('people', 'document')->ignore($personId),
            ],

            'birth_date' => [
                'required',
                'date',
            ],

            'gender' => [
                'required',
                'in:male,female,other,not_specified',
            ],

            'email' => [
                'required',
                'email',
                Rule::unique('people', 'email')->ignore($personId),
            ],

            'phone' => [
                'nullable',
                'string',
            ],

            'address' => [
                'nullable',
                'string',
            ],

            // --- Dados Específicos do Professor ---
            'registration' => [
                'required',
                'string',
                'max:50',
                Rule::unique('teachers', 'registration')->ignore($teacher?->id),
            ],

            // --- Arquivos e UI ---
            'photo' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg',
                'max:2048'
            ],

            'remove_photo' => [
                'nullable',
                'boolean' 
            ],
        ];
    }

    /**
     * Customização das mensagens (Opcional)
     */
    public function messages(): array
    {
        return [
            'name.required' => 'O nome do professor é obrigatório.',
            'name.string' => 'O nome do professor deve ser um texto válido.',
            'name.max' => 'O nome do professor não pode ultrapassar 255 caracteres.',
            'document.required' => 'O CPF do professor é obrigatório.',
            'document.string' => 'O CPF do professor deve ser um texto válido.',
            'document.unique' => 'Este CPF já está cadastrado para outra pessoa.',
            'birth_date.required' => 'A data de nascimento do professor é obrigatória.',
            'birth_date.date' => 'A data de nascimento do professor deve ser válida.',
            'gender.required' => 'O gênero do professor é obrigatório.',
            'gender.in' => 'O gênero selecionado é inválido.',
            'email.required' => 'O e-mail do professor é obrigatório.',
            'email.email' => 'O e-mail do professor deve ser válido.',
            'email.unique' => 'Este e-mail já está cadastrado para outra pessoa.',
            'phone.string' => 'O telefone do professor deve ser um texto válido.',
            'address.string' => 'O endereço do professor deve ser um texto válido.',
            'registration.required' => 'A matrícula do professor é obrigatória.',
            'registration.string' => 'A matrícula do professor deve ser um texto válido.',
            'registration.max'  => 'A matrícula do professor não pode ultrapassar 50 caracteres.',
            'registration.unique'  => 'Esta matrícula já está em uso.',
            'photo.image' => 'O arquivo da foto deve ser uma imagem válida.',
            'photo.mimes' => 'A foto deve estar nos formatos jpeg, jpg ou png.',
            'photo.max' => 'A foto não pode ultrapassar 2 MB.',
            'remove_photo.boolean' => 'O campo de remoção da foto é inválido.',
        ];
    }
}
