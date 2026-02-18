<?php

namespace App\Enums;  

enum Priority: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';
    case CRITICAL = 'critical';
    case URGENT = 'urgent';

    public function label(): string
    {
        return match ($this) {
            self::LOW => 'Baixa',
            self::MEDIUM => 'Média',
            self::HIGH => 'Alta',
            self::CRITICAL => 'Crítica',
            self::URGENT => 'Urgente',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::LOW => 'info',
            self::MEDIUM => 'warning',
            self::HIGH => 'danger',
            self::CRITICAL => 'dark',
            self::URGENT => 'danger'
        };
    }
}
