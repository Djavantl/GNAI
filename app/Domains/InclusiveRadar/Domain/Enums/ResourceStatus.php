<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Domain\Enums;

enum ResourceStatus: string
{
    case AVAILABLE = 'available';
    case IN_USE = 'in_use';
    case UNDER_MAINTENANCE = 'under_maintenance';
    case DAMAGED = 'damaged';
    case UNAVAILABLE = 'unavailable';

    public function label(): string
    {
        return match ($this) {
            self::AVAILABLE => 'Disponível',
            self::IN_USE => 'Em uso',
            self::UNDER_MAINTENANCE => 'Em manutenção',
            self::DAMAGED => 'Danificado',
            self::UNAVAILABLE => 'Indisponível',
        };
    }

    public function allowsLoan(): bool
    {
        return match ($this) {
            self::AVAILABLE,
            self::IN_USE => true,
            self::UNDER_MAINTENANCE,
            self::DAMAGED,
            self::UNAVAILABLE => false,
        };
    }

    public function blocksLoan(): bool
    {
        return ! $this->allowsLoan();
    }

    public function color(): string
    {
        return match ($this) {
            self::AVAILABLE => 'success',
            self::IN_USE => 'primary',
            self::UNDER_MAINTENANCE => 'warning',
            self::DAMAGED => 'danger',
            self::UNAVAILABLE => 'secondary',
        };
    }
}
