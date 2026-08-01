<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Infrastructure\Reporting;

use App\Domains\InclusiveRadar\Domain\Models\InstitutionalEvent;
use App\Domains\Reporting\Domain\Enums\ReportColumnType;
use App\Domains\Reporting\Infrastructure\Sources\EloquentReportSource;

final class InstitutionalEventsReportSource extends EloquentReportSource
{
    public function key(): string
    {
        return 'inclusive-radar.institutional-events';
    }

    public function label(): string
    {
        return 'Eventos institucionais';
    }

    protected function model(): string
    {
        return InstitutionalEvent::class;
    }

    protected function definitions(): array
    {
        return [
            'title' => ['label' => 'Título'], 'description' => ['label' => 'Descrição'],
            'start_date' => ['label' => 'Data inicial', 'type' => ReportColumnType::DATE],
            'end_date' => ['label' => 'Data final', 'type' => ReportColumnType::DATE],
            'start_time' => ['label' => 'Horário inicial'], 'end_time' => ['label' => 'Horário final'],
            'location' => ['label' => 'Local'], 'organizer' => ['label' => 'Organizador'],
            'audience' => ['label' => 'Público'], 'is_active' => ['label' => 'Ativo', 'type' => ReportColumnType::BOOLEAN],
        ];
    }

    protected function filterable(): array
    {
        return ['title', 'start_date', 'end_date', 'location', 'organizer', 'is_active'];
    }
}
