<?php

namespace App\Enums\InclusiveRadar;

enum ConservationState: string
{
    case NEW = 'novo';
    case GOOD = 'bom';
    case REGULAR = 'regular';
    case BAD = 'ruim';
    case NOT_APPLICABLE = 'naoaplicavel';

    public function label(): string
    {
        return match($this) {
            self::NEW => 'Novo',
            self::GOOD => 'Bom (Sinais de uso)',
            self::REGULAR => 'Regular (Avarias leves)',
            self::BAD => 'Ruim (Danificado)',
            self::NOT_APPLICABLE => 'Não se aplica',
        };
    }

    public function blocksLoan(): bool
    {
        return $this === self::BAD;
    }

    public function requiresMaintenance(): bool
    {
        return $this === self::BAD;
    }

    public function isUsable(): bool
    {
        return in_array($this, [
            self::NEW,
            self::GOOD,
            self::REGULAR,
        ]);
    }

    public function color(): string
    {
        return match($this) {
            self::NEW => 'success',
            self::GOOD => 'primary',
            self::REGULAR => 'warning',
            self::BAD => 'danger',
            self::NOT_APPLICABLE => 'secondary',
        };
    }
}
