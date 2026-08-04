<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Domain\DTOs\PedagogicalRecords;

use App\Domains\SpecializedEducationalSupport\Domain\Enums\PedagogicalFollowUpStatus;

final readonly class PedagogicalRecordDTO
{
    public function __construct(
        public string $followUpReason,
        public PedagogicalFollowUpStatus $followUpStatus,
        public string $duration,
        public bool $isPresent,
        public ?string $absenceReason,
        public ?string $systematicPedagogicalFollowUpRecord,
        public ?string $strategiesAndResourcesAdopted,
        public ?string $schoolAttendanceStatus,
        public ?string $referralsMade,
        public ?string $complementaryObservations,
    ) {}
}
