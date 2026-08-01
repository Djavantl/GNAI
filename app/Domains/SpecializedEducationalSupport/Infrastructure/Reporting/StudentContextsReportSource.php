<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Infrastructure\Reporting;

use App\Domains\Reporting\Domain\Enums\ReportColumnType;
use App\Domains\Reporting\Infrastructure\Sources\EloquentReportSource;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Pei;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;
use App\Domains\SpecializedEducationalSupport\Domain\Models\StudentContext;

final class StudentContextsReportSource extends EloquentReportSource
{
    public function key(): string
    {
        return 'specialized-support.student-contexts';
    }

    public function label(): string
    {
        return 'Contextos dos alunos';
    }

    protected function model(): string
    {
        return StudentContext::class;
    }

    protected function with(): array
    {
        return ['student.person', 'semester', 'evaluator.person'];
    }

    protected function definitions(): array
    {
        return [
            'student' => ['label' => 'Aluno', 'path' => 'student.person.name'],
            'registration' => ['label' => 'Matrícula', 'path' => 'student.registration'],
            'semester' => ['label' => 'Semestre', 'path' => 'semester.label'],
            'evaluation_type' => ['label' => 'Tipo de avaliação'],
            'is_current' => ['label' => 'Contexto atual', 'type' => ReportColumnType::BOOLEAN],
            'evaluator' => ['label' => 'Avaliador', 'path' => 'evaluator.person.name'],
            'history' => ['label' => 'Histórico'], 'specific_educational_needs' => ['label' => 'Necessidades educacionais específicas'],
            'learning_level' => ['label' => 'Nível de aprendizagem'], 'attention_level' => ['label' => 'Nível de atenção'],
            'communication_type' => ['label' => 'Tipo de comunicação'], 'interaction_level' => ['label' => 'Nível de interação'],
            'autonomy_level' => ['label' => 'Nível de autonomia'],
            'needs_mobility_support' => ['label' => 'Necessita apoio de mobilidade', 'type' => ReportColumnType::BOOLEAN],
            'needs_communication_support' => ['label' => 'Necessita apoio de comunicação', 'type' => ReportColumnType::BOOLEAN],
            'needs_pedagogical_adaptation' => ['label' => 'Necessita adaptação pedagógica', 'type' => ReportColumnType::BOOLEAN],
            'uses_assistive_technology' => ['label' => 'Usa tecnologia assistiva', 'type' => ReportColumnType::BOOLEAN],
            'has_medical_report' => ['label' => 'Possui laudo médico', 'type' => ReportColumnType::BOOLEAN],
            'uses_medication' => ['label' => 'Usa medicação', 'type' => ReportColumnType::BOOLEAN],
            'version' => ['label' => 'Versão do contexto'],
        ];
    }

    protected function filterable(): array
    {
        return ['student', 'registration', 'semester', 'evaluation_type', 'is_current', 'evaluator', 'learning_level', 'has_medical_report'];
    }

    protected function relationPolicy(string $parentModel, string $relationName): array
    {
        $excluded = match ($parentModel) {
            Student::class => ['student', 'registration'],
            Pei::class => ['student', 'registration', 'semester'],
            default => [],
        };

        return ['exclude' => $excluded];
    }
}
