<?php

namespace App\Http\Requests\SpecializedEducationalSupport;

use Illuminate\Foundation\Http\FormRequest;

class StudentDeficienciesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'deficiency_id' => ['required', 'exists:deficiencies,id'],
            'severity' => ['nullable', 'in:mild,moderate,severe'],
            'uses_support_resources' => ['boolean'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'uses_support_resources' => $this->has('uses_support_resources'),
        ]);
    }
}