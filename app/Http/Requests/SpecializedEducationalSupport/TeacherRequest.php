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
            'registration.unique'  => 'Esta matrícula já está em uso.',
        ];
    }
}