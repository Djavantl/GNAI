<?php

namespace App\Http\Requests\SpecializedEducationalSupport;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\SpecializedEducationalSupport\StudentStatus;
use App\Rules\Cpf;

class StudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $student = $this->route('student');
        $personId = $student?->person_id;
        $studentId = $student?->id;

        return [
            // pessoa

            'name' => [
                'required',
                'string',
                'max:255'
            ],

            'document' => [
                'nullable',
                'string',
                new Cpf, 
                Rule::unique('people', 'document')->ignore($personId),
            ],
            
            'birth_date' => [
                'required',
                'date'
            ],

            'gender' => [
                'nullable',
                'in:male,female,other,not_specified'
            ],

            'email' => [
                'required',
                'email'
            ],

            'phone' => [
                'nullable',
                'string'
            ],

            'address' => [
                'nullable',
                'string'
            ],

            // aluno

            'registration' => [
                'required',
                'string',
                Rule::unique('students', 'registration')->ignore($studentId),
            ],

            'status' => [
                'nullable',
                Rule::enum(StudentStatus::class),
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
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome do aluno é obrigatório.',
            'name.string' => 'O nome do aluno deve ser um texto válido.',
            'name.max' => 'O nome do aluno não pode ultrapassar 255 caracteres.',
            'document.string' => 'O CPF do aluno deve ser um texto válido.',
            'document.unique' => 'Este CPF já está cadastrado para outra pessoa.',
            'birth_date.required' => 'A data de nascimento do aluno é obrigatória.',
            'birth_date.date' => 'A data de nascimento do aluno deve ser válida.',
            'gender.in' => 'O gênero selecionado é inválido.',
            'email.required' => 'O e-mail do aluno é obrigatório.',
            'email.email' => 'O e-mail do aluno deve ser válido.',
            'phone.string' => 'O telefone do aluno deve ser um texto válido.',
            'address.string' => 'O endereço do aluno deve ser um texto válido.',
            'registration.required' => 'A matrícula do aluno é obrigatória.',
            'registration.string' => 'A matrícula do aluno deve ser um texto válido.',
            'registration.unique' => 'Esta matrícula já está cadastrada para outro aluno.',
            'status.enum' => 'O status selecionado é inválido.',
            'photo.image' => 'O arquivo da foto deve ser uma imagem válida.',
            'photo.mimes' => 'A foto deve estar nos formatos jpeg, jpg ou png.',
            'photo.max' => 'A foto não pode ultrapassar 2 MB.',
            'remove_photo.boolean' => 'O campo de remoção da foto é inválido.',
        ];
    }
}
