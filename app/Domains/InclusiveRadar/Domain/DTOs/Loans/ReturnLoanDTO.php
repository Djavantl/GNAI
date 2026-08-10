<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Domain\DTOs\Loans;

use App\Domains\InclusiveRadar\Domain\Enums\LoanStatus;
use DateTimeInterface;

final readonly class ReturnLoanDTO
{
    public function __construct(
        public DateTimeInterface $returnDate,
        public LoanStatus $status,
        public ?string $observation,
    ) {}
}
