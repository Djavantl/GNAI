<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Domain\Enums;

enum AttendanceType: string
{
    case AEE = 'aee';
    case PEDAGOGICAL = 'pedagogical';

    public function label(): string
    {
        return match ($this) {
            self::AEE => 'Atendimento AEE',
            self::PEDAGOGICAL => 'Atendimento Pedagógico',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $type) => [$type->value => $type->label()])
            ->toArray();
    }

    public static function labelFor(?string $type): string
    {
        return self::tryFrom($type ?? self::AEE->value)?->label()
            ?? self::AEE->label();
    }

    public static function isAee(?string $type): bool
    {
        return ($type ?? self::AEE->value) === self::AEE->value;
    }

    public static function isPedagogical(?string $type): bool
    {
        return ($type ?? self::AEE->value) === self::PEDAGOGICAL->value;
    }
}
