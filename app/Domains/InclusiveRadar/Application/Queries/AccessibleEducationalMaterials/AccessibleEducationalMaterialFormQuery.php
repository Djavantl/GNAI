<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Queries\AccessibleEducationalMaterials;

use App\Domains\InclusiveRadar\Domain\Enums\ConservationState;
use App\Domains\InclusiveRadar\Domain\Enums\InspectionType;
use App\Domains\InclusiveRadar\Domain\Enums\ResourceStatus;
use App\Domains\InclusiveRadar\Domain\Models\AccessibleEducationalMaterial;
use App\Domains\InclusiveRadar\Domain\Models\AccessibilityFeature;
use App\Models\SpecializedEducationalSupport\Deficiency;

final class AccessibleEducationalMaterialFormQuery
{
    /**
     * @return array<string, mixed>
     */
    public function forCreation(): array
    {
        return $this->formOptions(InspectionType::INITIAL) + [
            'defaultStatus' => ResourceStatus::AVAILABLE->value,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function forUpdate(AccessibleEducationalMaterial $material): array
    {
        $material->loadMissing(['deficiencies', 'accessibilityFeatures']);

        return $this->formOptions(InspectionType::PERIODIC) + [
            'material' => $material,
            'activeLoans' => $material->loans()
                ->whereNull('return_date')
                ->count(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function formOptions(InspectionType $defaultInspection): array
    {
        return [
            'deficiencies' => Deficiency::query()->orderBy('name')->get(),
            'accessibilityFeatures' => AccessibilityFeature::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),
            'resourceStatuses' => collect(ResourceStatus::cases())
                ->mapWithKeys(
                    static fn (ResourceStatus $status): array => [
                        $status->value => $status->label(),
                    ],
                ),
            'conservationStates' => collect(ConservationState::cases())
                ->mapWithKeys(
                    static fn (ConservationState $state): array => [
                        $state->value => $state->label(),
                    ],
                ),
            'inspectionTypes' => collect(InspectionType::cases())
                ->mapWithKeys(
                    static fn (InspectionType $type): array => [
                        $type->value => $type->label(),
                    ],
                ),
            'defaultInspection' => $defaultInspection->value,
        ];
    }
}
