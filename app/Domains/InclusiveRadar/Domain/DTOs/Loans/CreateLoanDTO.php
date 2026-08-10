<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Domain\DTOs\Loans;

use App\Domains\InclusiveRadar\Domain\Enums\LoanableType;

final readonly class CreateLoanDTO
{
    public function __construct(
        public int $loanableId,
        public LoanableType $loanableType,
        public ?int $studentId,
        public ?int $professionalId,
        public int $registeredBy,
        public string $loanDate,
        public string $dueDate,
        public ?string $observation = null,
    ) {}
}
