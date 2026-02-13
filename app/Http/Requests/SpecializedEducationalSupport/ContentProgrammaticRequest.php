<?php

namespace App\Http\Requests\SpecializedEducationalSupport;

use Illuminate\Foundation\Http\FormRequest;

class ContentProgrammaticRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'pei_id' => ['required', 'exists:peis,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ];
    }
}