<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Domain\DTOs\Loans;

final readonly class UpdateLoanDTO
{
    public function __construct(
        public ?string $observation,
    ) {}
}
