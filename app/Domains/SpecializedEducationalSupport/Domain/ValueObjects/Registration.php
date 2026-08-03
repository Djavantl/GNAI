<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Domain\ValueObjects;

use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidRegistration;

final readonly class Registration
{
    private function __construct(
        private string $value,
    ) {
    }

    public static function from(string $value): self
    {
        $normalized = trim($value);

        if ($normalized === '') {
            throw new InvalidRegistration('Matrícula não pode ser vazia.');
        }

        if (mb_strlen($normalized) > 50) {
            throw new InvalidRegistration('Matrícula não pode ultrapassar 50 caracteres.');
        }

        return new self($normalized);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
