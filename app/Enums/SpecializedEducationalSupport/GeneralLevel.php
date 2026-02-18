<?php

namespace App\Enums\SpecializedEducationalSupport;
use App\Enums\HasEnumHelper;

enum GeneralLevel: string
{
    use HasEnumHelper;

    case VERY_LOW = 'very_low';
    case LOW = 'low';
    case ADEQUATE = 'adequate';
    case GOOD = 'good';
    case EXCELLENT = 'excellent';

    public function label(): string
    {
        return match($this) {
            self::VERY_LOW => 'Muito Baixo',
            self::LOW => 'Baixo',
            self::ADEQUATE => 'Adequado',
            self::GOOD => 'Bom',
            self::EXCELLENT => 'Excelente',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::VERY_LOW => 'danger',
            self::LOW => 'warning',
            self::ADEQUATE => 'info',
            self::GOOD => 'primary',
            self::EXCELLENT => 'success',
        };
    }
}