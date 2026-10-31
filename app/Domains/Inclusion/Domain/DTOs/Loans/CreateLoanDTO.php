<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Domain\DTOs\Loans;


final readonly class CreateLoanDTO
{
    public function __construct(
        public int $loanableId,
        public string $loanableType,
        public ?int $studentId,
        public ?int $professionalId,
        public int $registeredBy,
        public string $loanDate,
        public string $dueDate,
        public ?string $observation = null,
    ) {}
}
