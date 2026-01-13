<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssistiveTechnologyImageRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'images' => [
                'nullable',
                'array',
            ],
            'images.*' => [
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'images.*.image' => 'O arquivo enviado deve ser uma imagem válida.',
            'images.*.mimes' => 'A imagem deve estar em um dos formatos permitidos: JPG, JPEG, PNG ou WEBP.',
            'images.*.max'   => 'A imagem é muito grande. O tamanho máximo permitido é de 5MB por arquivo.',
        ];
    }
}
