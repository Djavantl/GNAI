<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Domain\DTOs\StudentDeficiencies;

use App\Domains\Inclusion\Domain\Enums\DeficiencySeverity;

final readonly class CreateStudentDeficiencyDTO
{
    public function __construct(
        public ?DeficiencySeverity $severity = null,
        public ?string $notes = null,
    ) {}
}
