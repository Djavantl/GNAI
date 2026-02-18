<?php

namespace App\Enums\SpecializedEducationalSupport;

use App\Enums\HasEnumHelper;

enum EvaluationType: string
{
    use HasEnumHelper;

    case INITIAL = 'initial';
    case PERIODIC_REVIEW = 'periodic_review';
    case PEI_REVIEW = 'pei_review';
    case SPECIFIC_DEMAND = 'specific_demand';

    public function label(): string
    {
        return match($this) {
            self::INITIAL => 'Avaliação Inicial',
            self::PERIODIC_REVIEW => 'Revisão Periódica',
            self::PEI_REVIEW => 'Revisão do PEI',
            self::SPECIFIC_DEMAND => 'Demanda Específica',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::INITIAL => 'primary',
            self::PERIODIC_REVIEW => 'info',
            self::PEI_REVIEW => 'warning',
            self::SPECIFIC_DEMAND => 'secondary',
        };
    }
}