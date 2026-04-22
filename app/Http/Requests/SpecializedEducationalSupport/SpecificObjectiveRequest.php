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
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:1000'],
            'status' => ['required', new Enum(ObjectiveStatus::class)],
            'observations_progress' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'pei_id.required' => 'O PEI é obrigatório.',
            'pei_id.exists' => 'O PEI informado é inválido.',
            'title.required' => 'O título do objetivo específico é obrigatório.',
            'title.string' => 'O título do objetivo específico deve ser um texto válido.',
            'title.max' => 'O título do objetivo específico não pode ultrapassar 255 caracteres.',
            'status.Illuminate\Validation\Rules\Enum' => 'O status selecionado é inválido.',
            'description.required' => 'A descrição do objetivo é obrigatória.',
            'description.string' => 'A descrição do objetivo deve ser um texto válido.',
            'description.max' => 'A descrição do objetivo não pode ultrapassar 1000 caracteres.',
            'observations_progress.string' => 'As observações de progresso devem ser um texto válido.',
            'observations_progress.max' => 'As observações de progresso não podem ultrapassar 1000 caracteres.',
        ];
    }
}
