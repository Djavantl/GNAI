<?php

declare(strict_types=1);

namespace app\Domains\SpecializedEducationalSupport\Domain\Enums;

enum AttentionLevel: string
{
    case VERY_LOW = 'very_low';
    case LOW = 'low';
    case MODERATE = 'moderate';
    case HIGH = 'high';

    public function label(): string
    {
        return match ($this) {
            self::VERY_LOW => 'Muito Baixo',
            self::LOW => 'Baixo',
            self::MODERATE => 'Moderado',
            self::HIGH => 'Alto',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::VERY_LOW,
            self::LOW => 'danger',
            self::MODERATE => 'warning',
            self::HIGH => 'success',
        };
    }
}
