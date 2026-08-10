<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Data\AeeRecords\Concerns;

trait HasAeeRecordValidation
{
    protected static function contentRules(): array
    {
        return [
            'duration' => ['required', 'string', 'max:50'],
            'activities_performed' => ['required', 'string', 'max:100000'],
            'strategies_used' => ['nullable', 'string', 'max:100000'],
            'resources_used' => ['nullable', 'string', 'max:100000'],
            'general_observations' => ['nullable', 'string', 'max:100000'],
            'evaluations' => ['required', 'array', 'min:1'],
            'evaluations.*.student_id' => ['required', 'integer', 'distinct', 'exists:students,id'],
            'evaluations.*.is_present' => ['required', 'boolean'],
            'evaluations.*.absence_reason' => ['required_if:evaluations.*.is_present,0', 'nullable', 'string', 'max:100000'],
            'evaluations.*.student_participation' => ['required_if:evaluations.*.is_present,1', 'nullable', 'string', 'max:100000'],
            'evaluations.*.development_evaluation' => ['required_if:evaluations.*.is_present,1', 'nullable', 'string', 'max:100000'],
            'evaluations.*.adaptations_made' => ['nullable', 'string', 'max:100000'],
            'evaluations.*.progress_indicators' => ['nullable', 'string', 'max:100000'],
            'evaluations.*.recommendations' => ['nullable', 'string', 'max:100000'],
            'evaluations.*.next_session_adjustments' => ['nullable', 'string', 'max:100000'],
        ];
    }

    protected static function prepareEvaluations(array $properties): array
    {
        foreach (($properties['evaluations'] ?? []) as $index => $evaluation) {
            $properties['evaluations'][$index]['is_present'] = filter_var($evaluation['is_present'] ?? false, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        }

        return $properties;
    }

    public static function messages(): array
    {
        return [
            'attendance_session_id.required' => 'O agendamento do atendimento é obrigatório.',
            'attendance_session_id.exists' => 'O agendamento informado não existe.',
            'duration.required' => 'A duração do atendimento é obrigatória.',
            'duration.max' => 'A duração não pode ultrapassar 50 caracteres.',
            'activities_performed.required' => 'As atividades realizadas são obrigatórias.',
            'evaluations.required' => 'Informe ao menos uma avaliação de aluno.',
            'evaluations.min' => 'Informe ao menos uma avaliação de aluno.',
            'evaluations.*.student_id.distinct' => 'O mesmo aluno não pode aparecer mais de uma vez.',
            'evaluations.*.absence_reason.required_if' => 'A justificativa é obrigatória para aluno ausente.',
            'evaluations.*.student_participation.required_if' => 'A participação é obrigatória para aluno presente.',
            'evaluations.*.development_evaluation.required_if' => 'A avaliação de desenvolvimento é obrigatória para aluno presente.',
        ];
    }
}
