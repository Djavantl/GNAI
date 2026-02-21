<?php

namespace App\Http\Requests\SpecializedEducationalSupport;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
                'required',
                'string',
                'max:20',
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
        ];
    }
}
