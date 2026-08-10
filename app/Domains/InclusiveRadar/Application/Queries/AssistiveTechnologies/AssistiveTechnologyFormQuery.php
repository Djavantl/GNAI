<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Queries\AssistiveTechnologies;

use App\Domains\InclusiveRadar\Domain\Enums\ConservationState;
use App\Domains\InclusiveRadar\Domain\Enums\InspectionType;
use App\Domains\InclusiveRadar\Domain\Enums\ResourceStatus;
use App\Domains\InclusiveRadar\Domain\Models\AssistiveTechnology;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Deficiency;

final class AssistiveTechnologyFormQuery
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
    public function forCloning(AssistiveTechnology $technology): array
    {
        $technology->loadMissing('deficiencies');

        return $this->forCreation() + [
            'cloneSource' => $technology,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function forUpdate(AssistiveTechnology $technology): array
    {
        $technology->loadMissing('deficiencies');

        return $this->formOptions(InspectionType::PERIODIC) + [
            'assistiveTechnology' => $technology,
            'activeLoans' => $technology->loans()
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
