<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Domain\DTOs\Loans;

use App\Domains\Inclusion\Domain\Enums\LoanStatus;
use DateTimeInterface;

final readonly class ReturnLoanDTO
{
    public function __construct(
        public DateTimeInterface $returnDate,
        public LoanStatus $status,
        public ?string $observation,
    ) {}
}
