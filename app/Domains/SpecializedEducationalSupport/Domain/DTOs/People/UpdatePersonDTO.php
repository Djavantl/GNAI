<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Domain\DTOs\People;

use App\Domains\SpecializedEducationalSupport\Domain\Enums\Gender;
use App\Domains\SpecializedEducationalSupport\Domain\ValueObjects\Cpf;
use App\Domains\SpecializedEducationalSupport\Domain\ValueObjects\Phone;

final readonly class UpdatePersonDTO
{
    public function __construct(
        public string $name,
        public string $birthDate,
        public Gender $gender,
        public ?Cpf $document = null,
        public ?string $email = null,
        public ?Phone $phone = null,
        public ?string $address = null,
        public ?string $photo = null,
    ) {}
}
