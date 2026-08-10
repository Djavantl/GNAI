<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Infrastructure\Reporting;

use App\Domains\InclusiveRadar\Domain\Models\Institution;
use App\Domains\Reporting\Domain\Enums\ReportColumnType;
use App\Domains\Reporting\Infrastructure\Sources\EloquentReportSource;

final class InstitutionsReportSource extends EloquentReportSource
{
    public function key(): string
    {
        return 'inclusive-radar.institutions';
    }

    public function label(): string
    {
        return 'Instituições';
    }

    protected function model(): string
    {
        return Institution::class;
    }

    protected function definitions(): array
    {
        return [
            'name' => ['label' => 'Nome'], 'short_name' => ['label' => 'Sigla'],
            'city' => ['label' => 'Cidade'], 'state' => ['label' => 'Estado'], 'district' => ['label' => 'Bairro'],
            'address' => ['label' => 'Endereço'], 'latitude' => ['label' => 'Latitude'], 'longitude' => ['label' => 'Longitude'],
            'is_active' => ['label' => 'Ativa', 'type' => ReportColumnType::BOOLEAN],
        ];
    }

    protected function filterable(): array
    {
        return ['name', 'short_name', 'city', 'state', 'is_active'];
    }
}
