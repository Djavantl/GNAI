<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Domain\Enums;

enum InspectionType: string
{
    case INITIAL = 'initial';
    case PERIODIC = 'periodic';
    case RETURN = 'return';
    case MAINTENANCE = 'maintenance';

    public function label(): string
    {
        return match ($this) {
            self::INITIAL => 'Vistoria Inicial',
            self::PERIODIC => 'Vistoria Periódica',
            self::RETURN => 'Retorno de Empréstimo',
            self::MAINTENANCE => 'Manutenção',
        };
    }
}
