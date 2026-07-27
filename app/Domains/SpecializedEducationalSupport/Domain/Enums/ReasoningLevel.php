<?php

declare(strict_types=1);

namespace app\Domains\SpecializedEducationalSupport\Domain\Enums;

enum ReasoningLevel: string
{
    case CONCRETE = 'concrete';
    case MIXED = 'mixed';
    case ABSTRACT = 'abstract';

    public function label(): string
    {
        return match ($this) {
            self::CONCRETE => 'Concreto',
            self::MIXED => 'Misto',
            self::ABSTRACT => 'Abstrato',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::CONCRETE => 'warning',
            self::MIXED => 'info',
            self::ABSTRACT => 'success',
        };
    }
}
