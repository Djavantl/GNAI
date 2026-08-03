<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Domain\DTOs\PedagogicalRecords;

final readonly class PedagogicalRecordDTO
{
    public function __construct(
        public string $duration,
        public bool $isPresent,
        public ?string $absenceReason,
        public ?string $plannedPerformedActivities,
        public ?string $pedagogicalRecord,
        public ?string $resourcesUsed,
        public ?string $generalObservations,
    ) {}
}
