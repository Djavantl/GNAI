<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Infrastructure\Reporting;

use App\Domains\InclusiveRadar\Domain\Models\Location;
use App\Domains\Reporting\Domain\Enums\ReportColumnType;
use App\Domains\Reporting\Infrastructure\Sources\EloquentReportSource;

final class LocationsReportSource extends EloquentReportSource
{
    public function key(): string
    {
        return 'inclusive-radar.locations';
    }

    public function label(): string
    {
        return 'Locais';
    }

    protected function model(): string
    {
        return Location::class;
    }

    protected function with(): array
    {
        return ['institution'];
    }

    protected function definitions(): array
    {
        return [
            'name' => ['label' => 'Nome'], 'institution' => ['label' => 'Instituição', 'path' => 'institution.name'],
            'type' => ['label' => 'Tipo'], 'description' => ['label' => 'Descrição'],
            'latitude' => ['label' => 'Latitude'], 'longitude' => ['label' => 'Longitude'],
            'is_active' => ['label' => 'Ativo', 'type' => ReportColumnType::BOOLEAN],
        ];
    }

    protected function filterable(): array
    {
        return ['name', 'institution', 'type', 'is_active'];
    }
}
