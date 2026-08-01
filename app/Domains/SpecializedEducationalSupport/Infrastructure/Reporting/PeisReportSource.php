<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Infrastructure\Reporting;

use App\Domains\Reporting\Domain\Enums\ReportColumnType;
use App\Domains\Reporting\Infrastructure\Sources\EloquentReportSource;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Pei;
use App\Domains\SpecializedEducationalSupport\Domain\Models\StudentContext;

final class PeisReportSource extends EloquentReportSource
{
    public function key(): string
    {
        return 'specialized-support.peis';
    }

    public function label(): string
    {
        return 'Planos educacionais individualizados';
    }

    protected function model(): string
    {
        return Pei::class;
    }

    protected function with(): array
    {
        return ['student.person', 'semester', 'course', 'creator'];
    }

    protected function definitions(): array
    {
        return [
            'student' => ['label' => 'Aluno', 'path' => 'student.person.name'],
            'registration' => ['label' => 'Matrícula', 'path' => 'student.registration'],
            'semester' => ['label' => 'Semestre', 'path' => 'semester.label'],
            'course' => ['label' => 'Curso', 'path' => 'course.name'],
            'version' => ['label' => 'Versão do PEI'],
            'is_current' => ['label' => 'PEI atual', 'type' => ReportColumnType::BOOLEAN],
            'is_finished' => ['label' => 'Finalizado', 'type' => ReportColumnType::BOOLEAN],
            'creator' => ['label' => 'Criado por', 'path' => 'creator.name'],
            'created_at' => ['label' => 'Criado em', 'type' => ReportColumnType::DATE],
        ];
    }

    protected function filterable(): array
    {
        return ['student', 'registration', 'semester', 'course', 'is_current', 'is_finished', 'creator', 'created_at'];
    }

    protected function relationPolicy(string $parentModel, string $relationName): array
    {
        $excluded = $parentModel === StudentContext::class
            ? ['student', 'registration', 'semester']
            : [];

        return ['exclude' => $excluded];
    }
}
