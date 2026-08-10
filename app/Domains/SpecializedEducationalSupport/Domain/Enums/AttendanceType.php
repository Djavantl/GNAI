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
            self::AEE => 'Atendimento Educacional Especializado',
            self::PEDAGOGICAL => 'Atendimento Pedagógico',
        };
    }

    public static function options(): array
    {
        return [
            self::AEE->value => self::AEE->label(),
            self::PEDAGOGICAL->value => self::PEDAGOGICAL->label(),
        ];
    }

    public static function labelFor(self|string|null $type): string
    {
        return self::tryFrom(self::valueOf($type))?->label()
            ?? self::AEE->label();
    }

    public static function isAee(self|string|null $type): bool
    {
        return self::valueOf($type) === self::AEE->value;
    }

    public static function isPedagogical(self|string|null $type): bool
    {
        return self::valueOf($type) === self::PEDAGOGICAL->value;
    }

    public static function valueOf(self|string|null $type): string
    {
        return $type instanceof self ? $type->value : ($type ?? self::AEE->value);
    }
}
