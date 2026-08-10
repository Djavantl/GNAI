<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Infrastructure\Reporting;

use App\Domains\Reporting\Infrastructure\Sources\EloquentReportSource;
use App\Domains\SpecializedEducationalSupport\Domain\Models\PeiDiscipline;

final class PeiDisciplinesReportSource extends EloquentReportSource
{
    public function key(): string
    {
        return 'specialized-support.pei-disciplines';
    }

    public function label(): string
    {
        return 'Disciplinas dos PEIs';
    }

    protected function model(): string
    {
        return PeiDiscipline::class;
    }

    protected function with(): array
    {
        return ['pei.student.person', 'pei.semester', 'discipline', 'teacher.person', 'creator'];
    }

    protected function definitions(): array
    {
        return [
            'student' => ['label' => 'Aluno', 'path' => 'pei.student.person.name'],
            'semester' => ['label' => 'Semestre', 'path' => 'pei.semester.label'],
            'discipline' => ['label' => 'Disciplina', 'path' => 'discipline.name'],
            'teacher' => ['label' => 'Professor', 'path' => 'teacher.person.name'],
            'specific_objectives' => ['label' => 'Objetivos específicos'],
            'content_programmatic' => ['label' => 'Conteúdo programático'],
            'methodologies' => ['label' => 'Metodologias'],
            'evaluations' => ['label' => 'Avaliações'], 'opinion' => ['label' => 'Parecer'],
            'complementary_records' => ['label' => 'Registros complementares'],
            'creator' => ['label' => 'Criado por', 'path' => 'creator.name'],
        ];
    }

    protected function filterable(): array
    {
        return ['student', 'semester', 'discipline', 'teacher', 'creator'];
    }
}
