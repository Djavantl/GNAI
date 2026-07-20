<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Domain\DTOs\Waitlists;

use App\Domains\InclusiveRadar\Domain\Enums\WaitlistStatus;

final readonly class UpdateWaitlistDTO
{
    public function __construct(
        public ?WaitlistStatus $status,
        public ?string $observation,
    ) {}
}
