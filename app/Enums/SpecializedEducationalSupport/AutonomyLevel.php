<?php

namespace App\Enums\SpecializedEducationalSupport;
use App\Enums\HasEnumHelper;

enum AutonomyLevel: string
{
    use HasEnumHelper;

    case DEPENDENT = 'dependent';
    case PARTIAL = 'partial';
    case INDEPENDENT = 'independent';

    public function label(): string
    {
        return match($this) {
            self::DEPENDENT => 'Dependente',
            self::PARTIAL => 'Parcial',
            self::INDEPENDENT => 'Independente',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::DEPENDENT => 'danger',
            self::PARTIAL => 'warning',
            self::INDEPENDENT => 'success',
        };
    }
}