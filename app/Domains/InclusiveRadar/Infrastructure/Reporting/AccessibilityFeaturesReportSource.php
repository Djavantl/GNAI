<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Infrastructure\Reporting;

use App\Domains\InclusiveRadar\Domain\Models\AccessibilityFeature;
use App\Domains\Reporting\Domain\Enums\ReportColumnType;
use App\Domains\Reporting\Infrastructure\Sources\EloquentReportSource;

final class AccessibilityFeaturesReportSource extends EloquentReportSource
{
    public function key(): string
    {
        return 'inclusive-radar.accessibility-features';
    }

    public function label(): string
    {
        return 'Recursos de acessibilidade';
    }

    protected function model(): string
    {
        return AccessibilityFeature::class;
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
