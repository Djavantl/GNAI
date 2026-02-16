<?php

namespace App\Http\Requests\SpecializedEducationalSupport;

use App\Enums\SpecializedEducationalSupport\StudentDocumentType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StudentDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'      => ['required', 'string', 'max:255'],
            'type'       => ['required', new Enum(StudentDocumentType::class)],
            'version'    => ['nullable', 'integer'],
            
            'file' => ['sometimes', 'file', 'max:10240'], 
        ];
    }
}