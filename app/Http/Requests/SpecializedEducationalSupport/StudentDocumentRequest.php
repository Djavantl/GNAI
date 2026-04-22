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

    public function messages(): array
    {
        return [
            'title.required' => 'O título do documento é obrigatório.',
            'title.string' => 'O título do documento deve ser um texto válido.',
            'title.max' => 'O título do documento não pode ultrapassar 255 caracteres.',
            'type.required' => 'O tipo do documento é obrigatório.',
            'type.enum' => 'O tipo de documento selecionado é inválido.',
            'version.integer' => 'A versão do documento deve ser um número inteiro.',
            'file.file' => 'O arquivo enviado é inválido.',
            'file.max' => 'O arquivo não pode ultrapassar 10 MB.',
        ];
    }
}
