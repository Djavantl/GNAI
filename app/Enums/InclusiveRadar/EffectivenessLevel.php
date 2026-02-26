<?php

namespace App\Enums\InclusiveRadar;

enum EffectivenessLevel: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';

    public function label(): string
    {
        return match($this) {
            self::LOW => 'Baixo',
            self::MEDIUM => 'Médio',
            self::HIGH => 'Alto',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::LOW => 'danger',
            self::MEDIUM => 'warning',
            self::HIGH => 'success',
        };
    }
}
