<?php

namespace App\Http\Requests\InclusiveRadar;

use App\Enums\InclusiveRadar\ConservationState;
use App\Enums\InclusiveRadar\InspectionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class InspectionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'state' => ['nullable', new Enum(ConservationState::class)],
            'inspection_date' => 'required|date|before_or_equal:today',
            'description' => 'nullable|string|max:1000',
            'type' => ['required', new Enum(InspectionType::class)],
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'state.Illuminate\Validation\Rules\Enum' => 'O estado de conservação selecionado é inválido.',
            'type.Illuminate\Validation\Rules\Enum' => 'O tipo de inspeção selecionado é inválido.',
            'images.*.image' => 'O arquivo enviado deve ser uma imagem válida.',
            'inspection_date.before_or_equal' => 'A data da vistoria não pode ser no futuro.',
        ];
    }
}
