<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Domain\DTOs\Inspections;

use App\Domains\Inclusion\Domain\Enums\InspectionType;

final readonly class CreateInspectionDTO
{
    public function __construct(
        public string $date,
        public InspectionType $type,
        public int $registeredBy,
        public ?string $description = null,
        public ?string $state = null,
        public ?string $status = null,
    ) {}
}
