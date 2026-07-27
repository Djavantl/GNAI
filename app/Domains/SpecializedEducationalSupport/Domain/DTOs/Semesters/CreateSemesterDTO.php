<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Domain\DTOs\Semesters;

final readonly class CreateSemesterDTO
{
    public function __construct(
        public int $year,
        public int $term,
        public ?string $startDate = null,
        public ?string $endDate = null,
        public bool $isCurrent = false,
    ) {}
}
