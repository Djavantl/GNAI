<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Domain\DTOs\Waitlists;

use Carbon\CarbonInterface;

final readonly class CreateWaitlistDTO
{
    public function __construct(
        public int $waitlistableId,
        public string $waitlistableType,
        public ?int $studentId,
        public ?int $professionalId,
        public int $registeredBy,
        public CarbonInterface $requestedAt,
        public ?string $observation = null,
    ) {}
}
