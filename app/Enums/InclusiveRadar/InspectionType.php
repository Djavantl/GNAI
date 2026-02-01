<?php

namespace App\Enums\InclusiveRadar;

enum InspectionType: string
{
    case INITIAL = 'initial';
    case RETURN = 'return';
    case MAINTENANCE = 'maintenance';
    case PERIODIC = 'periodic';
    case RESOLUTION = 'resolution';

    public function label(): string
    {
        return match($this) {
            self::INITIAL => 'Vistoria Inicial',
            self::RETURN => 'Retorno de Empréstimo',
            self::MAINTENANCE => 'Manutenção',
            self::PERIODIC => 'Vistoria Periódica',
            self::RESOLUTION => 'Resolução de Pendência',
        };
    }
}
