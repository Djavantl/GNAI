<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Domain\Enums;

enum PedagogicalDisciplineCategory: string
{
    case FAILED = 'failed';
    case AT_ACADEMIC_RISK = 'at_academic_risk';

    public function label(): string
    {
        return match ($this) {
            self::FAILED => 'Com reprovação',
            self::AT_ACADEMIC_RISK => 'Com risco de insucesso acadêmico',
        };
    }
}
