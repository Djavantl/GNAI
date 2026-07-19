<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Domain\ValueObjects;

use App\Domains\InclusiveRadar\Domain\Exceptions\InvalidAssetCode;
use Stringable;

final readonly class AssetCode implements Stringable
{
    private const MAX_LENGTH = 50;

    private function __construct(
        private string $value,
    ) {}

    public static function from(string $value): self
    {
        $value = trim($value);

        if ($value === '') {
            throw new InvalidAssetCode(
                'O código patrimonial não pode ser vazio.'
            );
        }

        if (mb_strlen($value) > self::MAX_LENGTH) {
            throw new InvalidAssetCode(
                'O código patrimonial deve possuir no máximo 50 caracteres.'
            );
        }

        return new self($value);
    }

    public static function optional(?string $value): ?self
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

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
