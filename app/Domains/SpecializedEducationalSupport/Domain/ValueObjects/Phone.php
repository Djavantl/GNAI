<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Domain\ValueObjects;

use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidPhone;

final readonly class Phone
{
    private function __construct(
        private string $value,
    ) {
    }

    public static function from(string $value): self
    {
        $digits = preg_replace('/\D/', '', $value) ?? '';

        if (! in_array(strlen($digits), [10, 11], true)) {
            throw new InvalidPhone('Telefone inválido.');
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
        if (strlen($this->value) === 11) {
            return preg_replace(
                '/(\d{2})(\d{5})(\d{4})/',
                '($1) $2-$3',
                $this->value,
            ) ?? $this->value;
        }

        return preg_replace(
            '/(\d{2})(\d{4})(\d{4})/',
            '($1) $2-$3',
            $this->value,
        ) ?? $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
