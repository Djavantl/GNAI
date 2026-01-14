<?php

namespace App\Http\Requests\SpecializedEducationalSupport;

use Illuminate\Foundation\Http\FormRequest;

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
            'document'   => 'required|unique:people,document,' . ($this->person->id ?? ''),
            'birth_date' => 'required|date',
            'gender'     => 'required|in:male,female,other,not_specified',
            'email'      => 'required|email',
            'phone'      => 'nullable',
            'address'    => 'nullable|string',
        ];
    }
}
