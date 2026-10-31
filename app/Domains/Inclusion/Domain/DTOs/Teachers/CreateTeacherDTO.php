<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Domain\DTOs\Teachers;

use App\Domains\Inclusion\Domain\ValueObjects\Registration;

final readonly class CreateTeacherDTO
{
    public function __construct(
        public int $personId,
        public Registration $registration,
    ) {}
}
