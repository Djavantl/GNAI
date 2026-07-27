<?php

declare(strict_types=1);

namespace app\Domains\SpecializedEducationalSupport\Domain\ValueObjects;

use app\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidCpf;

final readonly class Cpf
{
    private function __construct(
        private string $value,
    ) {
    }

    public static function from(string $value): self
    {
        $digits = preg_replace('/\D/', '', $value) ?? '';

        if (! self::isValid($digits)) {
            throw new InvalidCpf('CPF inválido.');
        }

        return new self($digits);
    }

    public static function fromNullable(?string $value): ?self
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        return self::from($value);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function formatted(): string
    {
        return preg_replace(
            '/(\d{3})(\d{3})(\d{3})(\d{2})/',
            '$1.$2.$3-$4',
            $this->value,
        ) ?? $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    private static function isValid(string $digits): bool
    {
        if (strlen($digits) !== 11) {
            return false;
        }

        if (preg_match('/^(\d)\1{10}$/', $digits) === 1) {
            return false;
        }

        for ($position = 9; $position < 11; $position++) {
            $sum = 0;

            for ($index = 0; $index < $position; $index++) {
                $sum += (int) $digits[$index] * (($position + 1) - $index);
            }

            $digit = ((10 * $sum) % 11) % 10;

            if ((int) $digits[$position] !== $digit) {
                return false;
            }
        }

        return true;
    }
}
