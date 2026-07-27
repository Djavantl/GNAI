<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Domain\DTOs\Teachers;

use App\Domains\SpecializedEducationalSupport\Domain\ValueObjects\Registration;

final readonly class UpdateTeacherDTO
{
    public function __construct(
        public Registration $registration,
    ) {}
}
