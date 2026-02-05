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
                'required',
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
                // Adicionei o 'r' que faltava em grandmother no seu código original
                'in:mother,father,grandfather,grandmother,guardian,other'
            ],
        ];
    }

    /**
     * Customização das mensagens (opcional, mas melhora a UX)
     */
    public function messages(): array
    {
        return [
            'document.unique' => 'Este CPF/Documento já está cadastrado para outra pessoa.',
            'relationship.in' => 'O parentesco selecionado é inválido.',
            'birth_date.before' => 'A data de nascimento deve ser uma data passada.',
        ];
    }
}