<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Domain\DTOs\Sessions;

final readonly class UpdateSessionDTO
{
    /**
     * @param list<int> $studentIds
     */
    public function __construct(
        public int $professionalId,
        public array $studentIds,
        public string $sessionDate,
        public string $startTime,
        public string $endTime,
        public string $type,
        public string $attendanceType,
        public string $location,
        public string $sessionObjective,
        public string $status,
    ) {}
}
