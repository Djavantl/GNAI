<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Domain\DTOs\Students;

use App\Domains\SpecializedEducationalSupport\Domain\Enums\StudentStatus;
use App\Domains\SpecializedEducationalSupport\Domain\ValueObjects\Registration;

final readonly class CreateStudentDTO
{
    public function __construct(
        public Registration $registration,
        public ?string $entryDate = null,
        public bool $isRepeater = false,
        public StudentStatus $status = StudentStatus::ACTIVE,
    ) {}
}
