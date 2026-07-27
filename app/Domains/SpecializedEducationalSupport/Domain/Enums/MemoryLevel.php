<?php

declare(strict_types=1);

namespace app\Domains\SpecializedEducationalSupport\Domain\Enums;

enum MemoryLevel: string
{
    case LOW = 'low';
    case MODERATE = 'moderate';
    case GOOD = 'good';

    public function label(): string
    {
        return match ($this) {
            self::LOW => 'Baixo',
            self::MODERATE => 'Moderado',
            self::GOOD => 'Bom',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::LOW => 'danger',
            self::MODERATE => 'warning',
            self::GOOD => 'success',
        };
    }
}
