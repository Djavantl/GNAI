<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Infrastructure\Reporting;

use App\Domains\Reporting\Domain\Enums\ReportColumnType;
use App\Domains\Reporting\Infrastructure\Sources\EloquentReportSource;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Course;

final class CoursesReportSource extends EloquentReportSource
{
    public function key(): string
    {
        return 'specialized-support.courses';
    }

    public function label(): string
    {
        return 'Cursos';
    }

    protected function model(): string
    {
        return Course::class;
    }

    protected function definitions(): array
    {
        return [
            'name' => ['label' => 'Nome'], 'description' => ['label' => 'Descrição'],
            'is_active' => ['label' => 'Ativo', 'type' => ReportColumnType::BOOLEAN],
        ];
    }

    protected function filterable(): array
    {
        return ['name', 'is_active'];
    }
}
