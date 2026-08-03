<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Infrastructure\Reporting;

use App\Domains\InclusiveRadar\Domain\Enums\BarrierStatus;
use App\Domains\InclusiveRadar\Domain\Enums\ConservationState;
use App\Domains\InclusiveRadar\Domain\Enums\InspectionType;
use App\Domains\InclusiveRadar\Domain\Models\AccessibleEducationalMaterial;
use App\Domains\InclusiveRadar\Domain\Models\AssistiveTechnology;
use App\Domains\InclusiveRadar\Domain\Models\Barrier;
use App\Domains\InclusiveRadar\Domain\Models\Inspection;
use App\Domains\Reporting\Application\Services\ReportOptionCatalog;
use App\Domains\Reporting\Domain\DTOs\PolymorphicReportRelationDTO;
use App\Domains\Reporting\Domain\Enums\ReportColumnType;
use App\Domains\Reporting\Infrastructure\Sources\EloquentReportSource;

final class InspectionsReportSource extends EloquentReportSource
{
    public function key(): string
    {
        return 'inclusive-radar.inspections';
    }

    public function label(): string
    {
        return 'Vistorias';
    }

    protected function model(): string
    {
        return Inspection::class;
    }

    protected function with(): array
    {
        return ['inspectable', 'registeredBy'];
    }

    public function polymorphicRelations(): array
    {
        return [
            new PolymorphicReportRelationDTO(
                key: 'assistiveTechnology',
                label: 'Tecnologia assistiva',
                eloquentRelation: 'inspectable',
                morphType: 'assistive_technology',
                sourceKey: 'inclusive-radar.assistive-technologies',
            ),
            new PolymorphicReportRelationDTO(
                key: 'accessibleEducationalMaterial',
                label: 'Material pedagógico acessível',
                eloquentRelation: 'inspectable',
                morphType: 'accessible_educational_material',
                sourceKey: 'inclusive-radar.accessible-educational-materials',
            ),
            new PolymorphicReportRelationDTO(
                key: 'barrier',
                label: 'Barreira',
                eloquentRelation: 'inspectable',
                morphType: 'barrier',
                sourceKey: 'inclusive-radar.barriers',
            ),
        ];
    }

    protected function definitions(): array
    {
        return [
            'inspectable_type' => [
                'label' => 'Tipo do item vistoriado',
                'type' => ReportColumnType::SELECT,
                'options' => ReportOptionCatalog::inspectableTypes(),
            ],
            'inspectable' => ['label' => 'Item vistoriado', 'path' => 'inspectable.name'],
            'state' => ['label' => 'Estado de conservação', 'type' => ReportColumnType::SELECT, 'options' => $this->enumOptions(ConservationState::class)],
            'status' => ['label' => 'Situação', 'type' => ReportColumnType::SELECT, 'options' => $this->enumOptions(BarrierStatus::class)],
            'type' => ['label' => 'Tipo de vistoria', 'type' => ReportColumnType::SELECT, 'options' => $this->enumOptions(InspectionType::class)],
            'inspection_date' => ['label' => 'Data', 'type' => ReportColumnType::DATE],
            'description' => ['label' => 'Descrição'],
            'registered_by' => ['label' => 'Registrada por', 'path' => 'registeredBy.name'],
        ];
    }

    protected function filterable(): array
    {
        return ['inspectable_type', 'state', 'status', 'type', 'inspection_date', 'registered_by'];
    }

    protected function relationPolicy(string $parentModel, string $relationName): array
    {
        $excluded = in_array($parentModel, [
            AssistiveTechnology::class,
            AccessibleEducationalMaterial::class,
            Barrier::class,
        ], true) ? ['inspectable_type', 'inspectable'] : [];

        return ['exclude' => $excluded];
    }
}
