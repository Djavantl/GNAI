<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Domain\DTOs\Professionals;

use App\Domains\Inclusion\Domain\Enums\ProfessionalStatus;
use App\Domains\Inclusion\Domain\ValueObjects\Registration;

final readonly class UpdateProfessionalDTO
{
    public function __construct(
        public Registration $registration,
        public ProfessionalStatus $status,
        public string $entryDate,
    ) {}
}
