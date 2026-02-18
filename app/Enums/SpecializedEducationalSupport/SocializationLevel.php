<?php

namespace App\Enums\SpecializedEducationalSupport;
use App\Enums\HasEnumHelper;

enum SocializationLevel: string
{
    use HasEnumHelper;

    case ISOLATED = 'isolated';
    case SELECTIVE = 'selective';
    case PARTICIPATIVE = 'participative';

    public function label(): string
    {
        return match($this) {
            self::ISOLATED => 'Isolado',
            self::SELECTIVE => 'Seletivo',
            self::PARTICIPATIVE => 'Participativo',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::ISOLATED => 'danger',
            self::SELECTIVE => 'warning',
            self::PARTICIPATIVE => 'success',
        };
    }
}