<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Domain\DTOs\Waitlists;

use App\Domains\InclusiveRadar\Domain\Enums\LoanableType;
use Carbon\CarbonInterface;

final readonly class CreateWaitlistDTO
{
    public function __construct(
        public int $waitlistableId,
        public LoanableType $waitlistableType,
        public ?int $studentId,
        public ?int $professionalId,
        public int $registeredBy,
        public CarbonInterface $requestedAt,
        public ?string $observation = null,
    ) {}
}
