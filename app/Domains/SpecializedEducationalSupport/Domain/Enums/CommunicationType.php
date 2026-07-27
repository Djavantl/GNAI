<?php

declare(strict_types=1);

namespace app\Domains\SpecializedEducationalSupport\Domain\Enums;

enum CommunicationType: string
{
    case VERBAL = 'verbal';
    case NON_VERBAL = 'non_verbal';
    case MIXED = 'mixed';

    public function label(): string
    {
        return match ($this) {
            self::VERBAL => 'Verbal',
            self::NON_VERBAL => 'Não Verbal',
            self::MIXED => 'Mista',
        };
    }
}
