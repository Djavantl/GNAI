<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Infrastructure\Reporting;

use App\Domains\Reporting\Domain\Enums\ReportSourceVisibility;
use App\Domains\Reporting\Infrastructure\Sources\EloquentReportSource;
use App\Domains\SpecializedEducationalSupport\Domain\Models\StudentDeficiency;

final class StudentDeficienciesReportSource extends EloquentReportSource
{
    public function key(): string
    {
        return 'specialized-support.student-deficiencies';
    }

    public function label(): string
    {
        return 'Deficiências dos alunos';
    }

    public function visibility(): ReportSourceVisibility
    {
        return ReportSourceVisibility::RELATION_ONLY;
    }

    protected function model(): string
    {
        return StudentDeficiency::class;
    }

    protected function with(): array
    {
        return ['student.person', 'deficiency'];
    }

    protected function definitions(): array
    {
        return [
            'student' => ['label' => 'Aluno', 'path' => 'student.person.name'],
            'registration' => ['label' => 'Matrícula', 'path' => 'student.registration'],
            'deficiency' => ['label' => 'Deficiência', 'path' => 'deficiency.name'],
            'cid_code' => ['label' => 'Código CID', 'path' => 'deficiency.cid_code'],
            'severity' => ['label' => 'Severidade'], 'notes' => ['label' => 'Observações'],
        ];
    }

    protected function filterable(): array
    {
        return ['student', 'registration', 'deficiency', 'cid_code', 'severity'];
    }
}
