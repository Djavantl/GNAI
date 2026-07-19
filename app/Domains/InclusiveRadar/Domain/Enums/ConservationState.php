<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Domain\Enums;

enum ConservationState: string
{
    case NEW = 'novo';
    case GOOD = 'bom';
    case REGULAR = 'regular';
    case BAD = 'ruim';
    case NOT_APPLICABLE = 'naoaplicavel';

    public function label(): string
    {
        return match ($this) {
            self::NEW => 'Novo',
            self::GOOD => 'Bom (Sinais de uso)',
            self::REGULAR => 'Regular (Avarias leves)',
            self::BAD => 'Ruim (Danificado)',
            self::NOT_APPLICABLE => 'Não se aplica',
        };
    }

    public function allowsLoan(): bool
    {
        return $this !== self::BAD;
    }

    public function blocksLoan(): bool
    {
        return ! $this->allowsLoan();
    }

    public function requiresMaintenance(): bool
    {
        return $this === self::BAD;
    }
}
