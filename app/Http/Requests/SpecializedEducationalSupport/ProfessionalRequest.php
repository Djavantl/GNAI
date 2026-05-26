<?php

namespace App\Http\Requests\SpecializedEducationalSupport;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\Cpf;

class ProfessionalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $professional = $this->route('professional');
        $personId = $professional?->person_id;

        return [

            // Pessoa

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'document' => [
                'nullable',
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

            // Professional

            'registration' => [
                'required',
                'string',
                'max:50',
                Rule::unique('professionals', 'registration')
                    ->ignore($professional?->id),
            ],

            // Cargo

            'position_id' => [
                'required',
                'exists:positions,id',
            ],

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

            'is_admin' => [
                'nullable',
                'boolean',
            ],

            'status' => [
                'sometimes',
                'in:active,inactive',
            ],

        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome do profissional é obrigatório.',
            'name.string' => 'O nome do profissional deve ser um texto válido.',
            'name.max' => 'O nome do profissional não pode ultrapassar 255 caracteres.',
            'document.string' => 'O CPF do profissional deve ser um texto válido.',
            'document.unique' => 'Este CPF já está cadastrado para outra pessoa.',
            'birth_date.required' => 'A data de nascimento do profissional é obrigatória.',
            'birth_date.date' => 'A data de nascimento do profissional deve ser válida.',
            'gender.required' => 'O gênero do profissional é obrigatório.',
            'gender.in' => 'O gênero selecionado é inválido.',
            'email.required' => 'O e-mail do profissional é obrigatório.',
            'email.email' => 'O e-mail do profissional deve ser válido.',
            'email.unique' => 'Este e-mail já está cadastrado para outra pessoa.',
            'phone.string' => 'O telefone do profissional deve ser um texto válido.',
            'address.string' => 'O endereço do profissional deve ser um texto válido.',
            'registration.required' => 'A matrícula do profissional é obrigatória.',
            'registration.string' => 'A matrícula do profissional deve ser um texto válido.',
            'registration.max' => 'A matrícula do profissional não pode ultrapassar 50 caracteres.',
            'registration.unique' => 'Esta matrícula já está cadastrada para outro profissional.',
            'position_id.required' => 'O cargo do profissional é obrigatório.',
            'position_id.exists' => 'O cargo selecionado é inválido.',
            'photo.image' => 'O arquivo da foto deve ser uma imagem válida.',
            'photo.mimes' => 'A foto deve estar nos formatos jpeg, jpg ou png.',
            'photo.max' => 'A foto não pode ultrapassar 2 MB.',
            'remove_photo.boolean' => 'O campo de remoção da foto é inválido.',
            'is_admin.boolean' => 'O campo de administrador é inválido.',
            'status.in' => 'O status selecionado é inválido.',
        ];
    }
}
