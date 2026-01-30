<?php

namespace App\Http\Requests\SpecializedEducationalSupport;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GuardianRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $guardian = $this->route('guardian');
        $personId = $guardian?->person_id;

        return [
            // Pessoa do responsável
            'name' => [
                'required',
                'string',
                'max:255'
            ],

            'document' => [
                'required',
                'string',
                Rule::unique('people', 'document')->ignore($personId),
            ],

            'birth_date' => [
                'nullable',
                'date'
            ],

            'email' => [
                'nullable',
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

            // Responsável
            'relationship' => [
                'required',
                'in:Mãe,Pai,Avô,Avó,Responsável Legal,Outro'
            ],

        ];
    }
}
