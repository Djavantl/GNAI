<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Domain\DTOs\Professionals;

use App\Domains\SpecializedEducationalSupport\Domain\Enums\ProfessionalStatus;
use App\Domains\SpecializedEducationalSupport\Domain\ValueObjects\Registration;

final readonly class UpdateProfessionalDTO
{
    public function __construct(
        public Registration $registration,
        public ProfessionalStatus $status,
        public string $entryDate,
    ) {}
}
