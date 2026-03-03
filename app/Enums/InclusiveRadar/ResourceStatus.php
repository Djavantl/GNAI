<?php

namespace App\Enums\InclusiveRadar;

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

    public function description(): string
    {
        return match ($this) {
            self::AVAILABLE => 'Recurso disponível para uso e empréstimo.',
            self::IN_USE => 'Recurso atualmente em uso.',
            self::UNDER_MAINTENANCE => 'Recurso em manutenção ou reparo.',
            self::DAMAGED => 'Recurso danificado e indisponível temporariamente.',
            self::UNAVAILABLE => 'Recurso indisponível para acesso.',
        };
    }

    public function blocksLoan(): bool
    {
        return match ($this) {
            self::AVAILABLE => false,
            default => true,
        };
    }

    public function blocksAccess(): bool
    {
        return match ($this) {
            self::UNAVAILABLE => true,
            default => false,
        };
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
