<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Domain\DTOs\InstitutionalEvents;

final readonly class CreateInstitutionalEventDTO
{
    public function __construct(
        public string $title,
        public string $startDate,
        public string $endDate,
        public string $startTime,
        public string $endTime,
        public string $location,
        public ?string $description = null,
        public ?string $organizer = null,
        public ?string $audience = null,
        public bool $isActive = true,
    ) {}
}
