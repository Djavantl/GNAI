<?php

namespace App\Http\Requests\InclusiveRadar;

use Illuminate\Foundation\Http\FormRequest;

class InstitutionalEventRequest extends FormRequest
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

            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after_or_equal:start_time',

            'location' => 'nullable|string|max:255',
            'organizer' => 'nullable|string|max:255',
            'audience' => 'nullable|string|max:255',

            'is_active' => 'sometimes|boolean',
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
            'title.required' => 'O título do evento é obrigatório.',
            'title.max' => 'O título do evento não pode ultrapassar 255 caracteres.',

            'start_date.required' => 'A data de início do evento é obrigatória.',
            'start_date.date' => 'A data de início deve ser uma data válida.',
            'end_date.date' => 'A data de término deve ser uma data válida.',
            'end_date.after_or_equal' => 'A data de término não pode ser anterior à data de início.',

            'start_time.date_format' => 'O horário de início deve estar no formato HH:MM.',
            'end_time.date_format' => 'O horário de término deve estar no formato HH:MM.',
            'end_time.after_or_equal' => 'O horário de término não pode ser anterior ao horário de início.',
        ];
    }
}
