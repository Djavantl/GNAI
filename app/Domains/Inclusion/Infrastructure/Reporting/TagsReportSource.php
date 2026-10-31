<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Infrastructure\Reporting;

use App\Domains\Inclusion\Domain\Models\Tag;
use App\Domains\Reporting\Domain\Enums\ReportColumnType;
use App\Domains\Reporting\Infrastructure\Sources\EloquentReportSource;

final class TagsReportSource extends EloquentReportSource
{
    public function key(): string
    {
        return 'inclusive-radar.tags';
    }

    public function label(): string
    {
        return 'Etiquetas';
    }

    protected function model(): string
    {
        return Tag::class;
    }

    protected function definitions(): array
    {
        return [
            'name' => ['label' => 'Nome'],
            'is_active' => ['label' => 'Ativo', 'type' => ReportColumnType::BOOLEAN],
        ];
    }

    protected function filterable(): array
    {
        return ['name', 'is_active'];
    }
}
