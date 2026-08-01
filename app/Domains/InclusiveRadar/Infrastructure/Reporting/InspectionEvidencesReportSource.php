<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Infrastructure\Reporting;

use App\Domains\InclusiveRadar\Domain\Models\InspectionEvidence;
use App\Domains\Reporting\Application\Services\ReportOptionCatalog;
use App\Domains\Reporting\Domain\Enums\ReportColumnType;
use App\Domains\Reporting\Infrastructure\Sources\EloquentReportSource;

final class InspectionEvidencesReportSource extends EloquentReportSource
{
    public function key(): string
    {
        return 'inclusive-radar.inspection-evidences';
    }

    public function label(): string
    {
        return 'Evidências de vistorias';
    }

    protected function model(): string
    {
        return InspectionEvidence::class;
    }

    protected function with(): array
    {
        return ['inspection.inspectable'];
    }

    protected function definitions(): array
    {
        return [
            'inspectable_type' => [
                'label' => 'Tipo do item',
                'path' => 'inspection.inspectable_type',
                'type' => ReportColumnType::SELECT,
                'options' => ReportOptionCatalog::inspectableTypes(),
            ],
            'inspectable' => ['label' => 'Item vistoriado', 'path' => 'inspection.inspectable.name'],
            'original_name' => ['label' => 'Nome do arquivo'], 'mime_type' => ['label' => 'Formato'],
            'size' => ['label' => 'Tamanho (bytes)'],
        ];
    }

    protected function filterable(): array
    {
        return ['inspectable_type', 'original_name', 'mime_type'];
    }

    protected function relationPolicy(string $parentModel, string $relationName): array
    {
        return ['include' => ['original_name', 'mime_type', 'size']];
    }
}
