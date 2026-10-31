<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Infrastructure\Reporting;

use App\Domains\Inclusion\Domain\Enums\ConservationState;
use App\Domains\Inclusion\Domain\Enums\ItemType;
use App\Domains\Inclusion\Domain\Enums\ResourceStatus;
use App\Domains\Inclusion\Domain\Models\Item;
use App\Domains\Reporting\Domain\Enums\ReportColumnType;
use App\Domains\Reporting\Infrastructure\Sources\EloquentReportSource;

final class ItemsReportSource extends EloquentReportSource
{
    public function key(): string
    {
        return 'inclusive-radar.items';
    }

    public function label(): string
    {
        return 'Itens (tecnologias assistivas e materiais pedagógicos)';
    }

    protected function model(): string
    {
        return Item::class;
    }

    protected function definitions(): array
    {
        return [
            'item_type' => ['label' => 'Tipo do item', 'type' => ReportColumnType::SELECT, 'options' => $this->enumOptions(ItemType::class)],
            'name' => ['label' => 'Descrição'], 'asset_code' => ['label' => 'Código patrimonial'],
            'is_digital' => ['label' => 'Digital', 'type' => ReportColumnType::BOOLEAN],
            'is_loanable' => ['label' => 'Emprestável', 'type' => ReportColumnType::BOOLEAN],
            'quantity' => ['label' => 'Quantidade'], 'quantity_available' => ['label' => 'Quantidade disponível'],
            'conservation_state' => ['label' => 'Conservação', 'type' => ReportColumnType::SELECT, 'options' => $this->enumOptions(ConservationState::class)],
            'status' => ['label' => 'Situação', 'type' => ReportColumnType::SELECT, 'options' => $this->enumOptions(ResourceStatus::class)],
            'notes' => ['label' => 'Observações'], 'is_active' => ['label' => 'Ativo', 'type' => ReportColumnType::BOOLEAN],
        ];
    }

    protected function filterable(): array
    {
        return ['item_type', 'name', 'asset_code', 'is_digital', 'is_loanable', 'conservation_state', 'status', 'is_active'];
    }
}
