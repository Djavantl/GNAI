<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Domain\DTOs\Teachers;

use App\Domains\Inclusion\Domain\ValueObjects\Registration;

final readonly class UpdateTeacherDTO
{
    public function __construct(
        public Registration $registration,
    ) {}
}
