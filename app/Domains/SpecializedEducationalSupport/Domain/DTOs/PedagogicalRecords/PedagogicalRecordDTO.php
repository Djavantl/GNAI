<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Domain\DTOs\PedagogicalRecords;

final readonly class PedagogicalRecordDTO
{
    public function __construct(
        public string $followUpReason,
        public string $duration,
        public bool $isPresent,
        public ?string $absenceReason,
        public ?string $systematicPedagogicalFollowUpRecord,
        public ?string $strategiesAndResourcesAdopted,
        public ?string $referralsMade,
        public ?string $complementaryObservations,
        public bool $withGuardians = false,
    ) {}
}
