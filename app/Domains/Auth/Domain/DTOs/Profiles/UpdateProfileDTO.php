<?php

declare(strict_types=1);

namespace App\Domains\Auth\Domain\DTOs\Profiles;

use App\Domains\SpecializedEducationalSupport\Domain\Enums\Gender;

final readonly class UpdateProfileDTO
{
    public function __construct(
        public string $name,
        public string $registration,
        public string $birthDate,
        public Gender $gender,
        public string $email,
        public ?string $document = null,
        public ?string $phone = null,
        public ?string $address = null,
        public ?string $password = null,
    ) {}
}
