<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Domain\Enums;

enum LoanStatus: string
{
    case ACTIVE = 'active';
    case RETURNED = 'returned';
    case LATE = 'late';
    case DAMAGED = 'damaged';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Ativo (Com o Beneficiário)',
            self::RETURNED => 'Devolvido (No prazo)',
            self::LATE => 'Devolvido (Com atraso)',
            self::DAMAGED => 'Devolvido (Com avaria)',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::ACTIVE => 'success',
            self::RETURNED => 'primary',
            self::LATE => 'warning',
            self::DAMAGED => 'danger',
        };
    }

    public function isActive(): bool
    {
        return $this === self::ACTIVE;
    }

    public function isReturned(): bool
    {
        return in_array(
            $this,
            [self::RETURNED, self::LATE, self::DAMAGED],
            true,
        );
    }

    public function requiresMaintenance(): bool
    {
        return $this === self::DAMAGED;
    }

    /**
     * @return list<string>
     */
    public static function openStatuses(): array
    {
        return [
            self::ACTIVE->value,
            self::LATE->value,
        ];
    }
}
