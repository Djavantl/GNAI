<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Domain\DTOs\Professionals;

use App\Domains\SpecializedEducationalSupport\Domain\Enums\ProfessionalStatus;
use App\Domains\SpecializedEducationalSupport\Domain\ValueObjects\Registration;

final readonly class CreateProfessionalDTO
{
    public function __construct(
        public Registration $registration,
        public string $entryDate,
        public ProfessionalStatus $status = ProfessionalStatus::ACTIVE,
    ) {}
}
