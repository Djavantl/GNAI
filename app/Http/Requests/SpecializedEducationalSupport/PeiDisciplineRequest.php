<?php

namespace App\Http\Requests\SpecializedEducationalSupport;

use Illuminate\Foundation\Http\FormRequest;

class PeiDisciplineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'teacher_id' => 'required|exists:teachers,id',
            'discipline_id' => 'required|exists:disciplines,id',
            'specific_objectives' => 'required|string',
            'content_programmatic' => 'required|string',
            'methodologies' => 'required|string',
            'evaluations' => 'required|string',
        ];
    }
}