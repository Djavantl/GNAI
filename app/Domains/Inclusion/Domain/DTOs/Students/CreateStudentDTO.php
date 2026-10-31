<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Domain\DTOs\Students;

use App\Domains\Inclusion\Domain\Enums\StudentStatus;
use App\Domains\Inclusion\Domain\ValueObjects\Registration;

final readonly class CreateStudentDTO
{
    public function __construct(
        public Registration $registration,
        public ?string $entryDate = null,
        public StudentStatus $status = StudentStatus::ACTIVE,
    ) {}
}
