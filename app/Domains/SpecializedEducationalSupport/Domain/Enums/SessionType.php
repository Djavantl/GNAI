<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Domain\Enums;

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

    public static function labelFor(?string $type): string
    {
        return self::tryFrom((string) $type)?->label() ?? ucfirst((string) $type);
    }

    public static function options(): array
    {
        return [
            self::INDIVIDUAL->value => self::INDIVIDUAL->label(),
            self::GROUP->value => self::GROUP->label(),
        ];
    }
}
