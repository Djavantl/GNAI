<?php

namespace App\Enums\InclusiveRadar;

enum InspectionType: string
{
    case INITIAL = 'initial';
    case PERIODIC = 'periodic';
    case RETURN = 'return';
    case MAINTENANCE = 'maintenance';

    public function label(): string
    {
        return match($this) {
            self::INITIAL => 'Vistoria Inicial',
            self::PERIODIC => 'Vistoria Periódica',
            self::RETURN => 'Retorno de Empréstimo',
            self::MAINTENANCE => 'Manutenção',
        };
    }
}
