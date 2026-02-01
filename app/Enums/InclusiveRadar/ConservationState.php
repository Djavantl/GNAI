<?php

namespace App\Enums\InclusiveRadar;

enum ConservationState: string
{
    case NEW = 'novo';
    case GOOD = 'bom';
    case REGULAR = 'regular';
    case BAD = 'ruim';
    case MAINTENANCE = 'manutencao';
    case NOT_APPLICABLE = 'naoaplicavel';

    public function label(): string
    {
        return match($this) {
            self::NEW => 'Novo',
            self::GOOD => 'Bom (Sinais de uso)',
            self::REGULAR => 'Regular (Avarias leves)',
            self::BAD => 'Ruim (Danificado)',
            self::MAINTENANCE => 'Necessita Manutenção',
            self::NOT_APPLICABLE => 'Não se aplica',
        };
    }
}
