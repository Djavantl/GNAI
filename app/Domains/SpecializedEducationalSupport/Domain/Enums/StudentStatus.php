<?php

namespace app\Domains\SpecializedEducationalSupport\Domain\Enums;

enum StudentStatus: string
{

    case ACTIVE = 'active';
    case LOCKED = 'locked';
    case COMPLETED = 'completed';
    case DROPPED = 'dropped';

    public function label(): string
    {
        return match($this) {
            self::ACTIVE => 'Ativo',
            self::LOCKED => 'Trancado',
            self::COMPLETED => 'Concluído',
            self::DROPPED => 'Desistente',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::ACTIVE => 'success',
            self::LOCKED => 'warning',
            self::COMPLETED => 'primary',
            self::DROPPED => 'danger',
        };
    }
}