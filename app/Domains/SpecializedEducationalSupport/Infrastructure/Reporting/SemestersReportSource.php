<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Infrastructure\Reporting;

use App\Domains\Reporting\Domain\Enums\ReportColumnType;
use App\Domains\Reporting\Infrastructure\Sources\EloquentReportSource;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Pei;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Semester;
use App\Domains\SpecializedEducationalSupport\Domain\Models\StudentContext;
use App\Domains\SpecializedEducationalSupport\Domain\Models\StudentDocument;

final class SemestersReportSource extends EloquentReportSource
{
    public function key(): string
    {
        return 'specialized-support.semesters';
    }

    public function label(): string
    {
        return 'Semestres';
    }

    protected function model(): string
    {
        return Semester::class;
    }

    protected function definitions(): array
    {
        return [
            'label' => ['label' => 'Período'], 'year' => ['label' => 'Ano'], 'term' => ['label' => 'Número do semestre'],
            'start_date' => ['label' => 'Início', 'type' => ReportColumnType::DATE],
            'end_date' => ['label' => 'Fim', 'type' => ReportColumnType::DATE],
            'is_current' => ['label' => 'Atual', 'type' => ReportColumnType::BOOLEAN],
        ];
    }

    protected function filterable(): array
    {
        return ['label', 'year', 'is_current', 'start_date', 'end_date'];
    }

    protected function relationPolicy(string $parentModel, string $relationName): array
    {
        $excluded = in_array($parentModel, [StudentDocument::class, StudentContext::class, Pei::class], true)
            ? ['label', 'year', 'term']
            : [];

        return ['exclude' => $excluded];
    }
}
