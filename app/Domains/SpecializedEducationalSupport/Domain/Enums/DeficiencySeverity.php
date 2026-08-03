<?php

declare(strict_types=1);
namespace App\Domains\SpecializedEducationalSupport\Domain\Enums;

enum DeficiencySeverity: string
{
    case MILD = 'mild';
    case MODERATE = 'moderate';
    case SEVERE = 'severe';

    public function label(): string
    {
        return match ($this) {
            self::MILD => 'Leve',
            self::MODERATE => 'Moderada',
            self::SEVERE => 'Severa',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::MILD => 'info',
            self::MODERATE => 'warning',
            self::SEVERE => 'danger',
        };
    }
}
