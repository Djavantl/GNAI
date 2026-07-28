<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Domain\DTOs\StudentDeficiencies;

use App\Domains\SpecializedEducationalSupport\Domain\Enums\DeficiencySeverity;

final readonly class CreateStudentDeficiencyDTO
{
    public function __construct(
        public ?DeficiencySeverity $severity = null,
        public ?string $notes = null,
    ) {}
}
