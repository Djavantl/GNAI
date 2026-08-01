<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Infrastructure\Reporting;

use App\Domains\InclusiveRadar\Domain\Models\BarrierCategory;
use App\Domains\Reporting\Domain\Enums\ReportColumnType;
use App\Domains\Reporting\Infrastructure\Sources\EloquentReportSource;

final class BarrierCategoriesReportSource extends EloquentReportSource
{
    public function key(): string
    {
        return 'inclusive-radar.barrier-categories';
    }

    public function label(): string
    {
        return 'Categorias de barreiras';
    }

    protected function model(): string
    {
        return BarrierCategory::class;
    }

    protected function definitions(): array
    {
        return [
            'name' => ['label' => 'Nome'], 'description' => ['label' => 'Descrição'],
            'blocks_map' => ['label' => 'Bloqueia mapa', 'type' => ReportColumnType::BOOLEAN],
            'is_active' => ['label' => 'Ativa', 'type' => ReportColumnType::BOOLEAN],
        ];
    }

    protected function filterable(): array
    {
        return ['name', 'blocks_map', 'is_active'];
    }
}
