<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Infrastructure\Reporting;

use App\Domains\Reporting\Domain\Enums\ReportColumnType;
use App\Domains\Reporting\Infrastructure\Sources\EloquentReportSource;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Deficiency;

final class DeficienciesReportSource extends EloquentReportSource
{
    public function key(): string
    {
        return 'specialized-support.deficiencies';
    }

    public function label(): string
    {
        return 'Deficiências';
    }

    protected function model(): string
    {
        return Deficiency::class;
    }

    protected function definitions(): array
    {
        return [
            'name' => ['label' => 'Nome'], 'cid_code' => ['label' => 'Código CID'],
            'description' => ['label' => 'Descrição'],
            'is_active' => ['label' => 'Ativa', 'type' => ReportColumnType::BOOLEAN],
        ];
    }

    protected function filterable(): array
    {
        return ['name', 'cid_code', 'is_active'];
    }
}
