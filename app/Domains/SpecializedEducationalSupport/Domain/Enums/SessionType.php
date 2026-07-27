<?php

declare(strict_types=1);

namespace app\Domains\SpecializedEducationalSupport\Domain\Enums;

enum SessionType: string
{
    case INDIVIDUAL = 'individual';
    case GROUP = 'group';

    public function label(): string
    {
        return match ($this) {
            self::INDIVIDUAL => 'Individual',
            self::GROUP => 'Grupo',
        };
    }
}
