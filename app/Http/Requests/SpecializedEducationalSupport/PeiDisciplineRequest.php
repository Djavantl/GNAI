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
            'opinion' => 'nullable|string',
            'complementary_records' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'teacher_id.required' => 'O professor responsável é obrigatório.',
            'teacher_id.exists' => 'O professor responsável informado é inválido.',
            'discipline_id.required' => 'A disciplina é obrigatória.',
            'discipline_id.exists' => 'A disciplina informada é inválida.',
            'specific_objectives.required' => 'Os objetivos específicos são obrigatórios.',
            'specific_objectives.string' => 'Os objetivos específicos devem ser um texto válido.',
            'content_programmatic.required' => 'O conteúdo programático é obrigatório.',
            'content_programmatic.string' => 'O conteúdo programático deve ser um texto válido.',
            'methodologies.required' => 'As metodologias e estratégias são obrigatórias.',
            'methodologies.string' => 'As metodologias e estratégias devem ser um texto válido.',
            'evaluations.required' => 'O processo de avaliação é obrigatório.',
            'evaluations.string' => 'O processo de avaliação deve ser um texto válido.',
            'opinion.string' => 'O parecer deve ser um texto válido.',
            'complementary_records.string' => 'Os registros complementares devem ser um texto válido.',
        ];
    }
}
