<?php

namespace App\Http\Requests\SpecializedEducationalSupport;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\SpecializedEducationalSupport\Guardian;

class GuardianRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Pegamos o objeto do responsável da rota (se existir) para ignorar o CPF dele mesmo na edição
        $guardian = $this->route('guardian');
        $personId = $guardian?->person_id;

        return [
            // --- Dados da Tabela 'people' ---
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255'
            ],

            'document' => [
                'nullable',
                'string',
                // Garante que o CPF/RG seja único, mas ignora o ID da pessoa atual se for um Update
                Rule::unique('people', 'document')->ignore($personId),
            ],

            'birth_date' => [
                'required', // Geralmente obrigatório para responsáveis
                'date',
                'before:today' // Não pode ter nascido no futuro
            ],

            'gender' => [
                'required',
                // Puxa as chaves (male, female, etc) do array que você criou no Model
                Rule::in(array_keys(Guardian::genderOptions())),
            ],

            'email' => [
                'nullable',
                'email',
                'max:255'
            ],

            'phone' => [
                'required', // Importante para a escola conseguir contato
                'string',
            ],

            'address' => [
                'nullable',
                'string',
                'max:500'
            ],

            // --- Dados da Tabela 'student_guardians' ---
            'relationship' => [
                'required',
                'string',
                Rule::in(array_keys(Guardian::relationshipOptions())),
            ],

            // --- Foto ---
            'photo'        => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
            'remove_photo' => ['nullable'],
        ];
    }

    /**
     * Customização das mensagens (opcional, mas melhora a UX)
     */
    public function messages(): array
    {
        return [
            'name.required' => 'O nome do responsável é obrigatório.',
            'name.string' => 'O nome do responsável deve ser um texto válido.',
            'name.min' => 'O nome do responsável deve ter ao menos 3 caracteres.',
            'name.max' => 'O nome do responsável não pode ultrapassar 255 caracteres.',
            'document.string' => 'O documento do responsável deve ser um texto válido.',
            'document.unique' => 'Este CPF/Documento já está cadastrado para outra pessoa.',
            'birth_date.required' => 'A data de nascimento do responsável é obrigatória.',
            'birth_date.date' => 'A data de nascimento do responsável deve ser válida.',
            'birth_date.before' => 'A data de nascimento deve ser uma data passada.',
            'gender.required' => 'O gênero do responsável é obrigatório.',
            'gender.in' => 'O gênero selecionado é inválido.',
            'email.email' => 'O e-mail do responsável deve ser válido.',
            'email.max' => 'O e-mail do responsável não pode ultrapassar 255 caracteres.',
            'phone.required' => 'O telefone do responsável é obrigatório.',
            'phone.string' => 'O telefone do responsável deve ser um texto válido.',
            'address.string' => 'O endereço do responsável deve ser um texto válido.',
            'address.max' => 'O endereço do responsável não pode ultrapassar 500 caracteres.',
            'relationship.required' => 'O parentesco é obrigatório.',
            'relationship.string' => 'O parentesco deve ser um texto válido.',
            'relationship.in' => 'O parentesco selecionado é inválido.',
        ];
    }
}
