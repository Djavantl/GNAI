<?php

namespace App\Http\Requests\SpecializedEducationalSupport;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\SpecializedEducationalSupport\ObjectiveStatus;
use Illuminate\Validation\Rules\Enum;

class SpecificObjectiveRequest extends FormRequest
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
            'description' => ['required', 'string', 'max:1000'],
            'status' => ['required', new Enum(ObjectiveStatus::class)],
            'observations_progress' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.Illuminate\Validation\Rules\Enum' => 'O status selecionado é inválido.',
            'description.required' => 'A descrição do objetivo é obrigatória.',
        ];
    }
}