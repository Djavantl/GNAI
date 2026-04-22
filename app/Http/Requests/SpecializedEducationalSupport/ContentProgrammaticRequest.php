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

    public function messages(): array
    {
        return [
            'pei_id.required' => 'O PEI é obrigatório.',
            'pei_id.exists' => 'O PEI informado é inválido.',
            'title.required' => 'O título do conteúdo programático é obrigatório.',
            'title.string' => 'O título do conteúdo programático deve ser um texto válido.',
            'title.max' => 'O título do conteúdo programático não pode ultrapassar 255 caracteres.',
            'description.string' => 'A descrição do conteúdo programático deve ser um texto válido.',
        ];
    }
}
