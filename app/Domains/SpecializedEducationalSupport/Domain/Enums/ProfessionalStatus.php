<?php

namespace app\Domains\SpecializedEducationalSupport\Domain\Enums;

enum ProfessionalStatus: string
{

    case ACTIVE = 'active';
    case INACTIVE = 'inactive';


    public function label(): string
    {
        return match($this) {
            self::ACTIVE => 'Ativo',
            self::INACTIVE => 'Inativo',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::ACTIVE => 'success',
            self::INACTIVE => 'danger',
        };
    }
}