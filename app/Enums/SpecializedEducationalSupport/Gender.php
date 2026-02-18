<?php

namespace App\Enums\SpecializedEducationalSupport;

use App\Enums\HasEnumHelper;

enum Gender: string
{
    use HasEnumHelper;

    case MALE = 'male';
    case FEMALE = 'female';
    case OTHER = 'other';
    case NOT_SPECIFIED = 'not_specified';

    public function label(): string
    {
        return match($this) {
            self::MALE => 'Masculino',
            self::FEMALE => 'Feminino',
            self::OTHER => 'Outro',
            self::NOT_SPECIFIED => 'Não Informado',
        };
    }
}