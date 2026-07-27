<?php

declare(strict_types=1);
namespace App\Domains\SpecializedEducationalSupport\Domain\Enums;


enum EvaluationType: string
{

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