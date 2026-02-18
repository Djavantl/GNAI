<?php

namespace App\Http\Requests\InclusiveRadar;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TrainingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'url' => 'nullable|array',
            'url.*' => 'nullable|url|max:1000',
            'is_active' => 'sometimes|boolean',

            'trainable_id' => 'required|integer',
            'trainable_type' => [
                'required',
                'string',
                Rule::in([
                    'assistive_technology',
                    'accessible_educational_material'
                ]),
            ],

            'files' => 'nullable|array',
            'files.*' => 'nullable|file|mimes:pdf,doc,docx,zip,jpg,png|max:10240',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
        ]);

    }

    public function messages(): array
    {
        return [
            'title.required' => 'O título do treinamento é obrigatório.',
            'title.max' => 'O título do treinamento não pode ultrapassar 255 caracteres.',
            'trainable_id.required' => 'Você deve selecionar uma TA ou um MPA para este treinamento.',
            'trainable_type.required' => 'O tipo de recurso é obrigatório.',
            'trainable_type.in' => 'O tipo de recurso selecionado é inválido.',
            'url.*.url' => 'Cada link informado deve ser uma URL válida.',
            'files.*.mimes' => 'Apenas arquivos PDF, DOC, DOCX, ZIP e imagens são permitidos.',
            'files.*.max' => 'Cada arquivo não pode ultrapassar 10MB.',
        ];
    }
}
