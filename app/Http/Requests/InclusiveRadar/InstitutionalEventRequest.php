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
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after_or_equal:start_time',

            'location' => 'required|string|max:255',
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
            'end_date.required' => 'A data de término do evento é obrigatória.',
            'end_date.date' => 'A data de término deve ser uma data válida.',
            'end_date.after_or_equal' => 'A data de término não pode ser anterior à data de início.',

            'start_time.required' => 'O horário de início do evento é obrigatório.',
            'start_time.date_format' => 'O horário de início deve estar no formato HH:MM.',

            'end_time.required' => 'O horário de término do evento é obrigatório.',
            'end_time.date_format' => 'O horário de término deve estar no formato HH:MM.',
            'end_time.after_or_equal' => 'O horário de término não pode ser anterior ao horário de início.',

            'location.required' => 'O local do evento é obrigatório.',
            'location.max' => 'O local do evento não pode ultrapassar 255 caracteres.',
        ];
    }
}
