<?php

namespace App\Enums\SpecializedEducationalSupport;
use App\Enums\HasEnumHelper;

enum IntensityLevel: string
{
    use HasEnumHelper;

    case VERY_LOW = 'very_low';
    case LOW = 'low';
    case MODERATE = 'moderate';
    case HIGH = 'high';
    case GOOD = 'good'; 

    public function label(): string
    {
        return match($this) {
            self::VERY_LOW => 'Muito Baixo',
            self::LOW => 'Baixo',
            self::MODERATE => 'Moderado',
            self::HIGH => 'Alto',
            self::GOOD => 'Bom',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::VERY_LOW, self::LOW => 'danger',
            self::MODERATE => 'warning',
            default => 'success',
        };
    }
}