<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Infrastructure\Reporting;

use App\Domains\InclusiveRadar\Domain\Enums\ConservationState;
use App\Domains\InclusiveRadar\Domain\Enums\ResourceStatus;
use App\Domains\InclusiveRadar\Domain\Models\AssistiveTechnology;
use App\Domains\Reporting\Domain\Enums\ReportColumnType;
use App\Domains\Reporting\Infrastructure\Sources\EloquentReportSource;

final class AssistiveTechnologiesReportSource extends EloquentReportSource
{
    public function key(): string
    {
        return 'inclusive-radar.assistive-technologies';
    }

    public function label(): string
    {
        return 'Tecnologias assistivas';
    }

    protected function model(): string
    {
        return AssistiveTechnology::class;
    }

    protected function definitions(): array
    {
        return [
            'name' => ['label' => 'Nome'], 'asset_code' => ['label' => 'Código patrimonial'],
            'is_digital' => ['label' => 'Digital', 'type' => ReportColumnType::BOOLEAN],
            'is_loanable' => ['label' => 'Emprestável', 'type' => ReportColumnType::BOOLEAN],
            'quantity' => ['label' => 'Quantidade'], 'quantity_available' => ['label' => 'Quantidade disponível'],
            'conservation_state' => ['label' => 'Conservação', 'type' => ReportColumnType::SELECT, 'options' => $this->enumOptions(ConservationState::class)],
            'status' => ['label' => 'Situação', 'type' => ReportColumnType::SELECT, 'options' => $this->enumOptions(ResourceStatus::class)],
            'notes' => ['label' => 'Observações'], 'is_active' => ['label' => 'Ativa', 'type' => ReportColumnType::BOOLEAN],
        ];
    }

    protected function filterable(): array
    {
        return ['name', 'asset_code', 'is_digital', 'is_loanable', 'conservation_state', 'status', 'is_active'];
    }
}
