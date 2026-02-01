<?php

namespace App\Enums\InclusiveRadar;

enum BarrierStatus: string
{
    case IDENTIFIED = 'identified';
    case UNDER_ANALYSIS = 'under_analysis';
    case IN_PROGRESS = 'in_progress';
    case RESOLVED = 'resolved';
    case NOT_APPLICABLE = 'not_applicable';

    public function label(): string
    {
        return match($this) {
            self::IDENTIFIED => 'Identificada',
            self::UNDER_ANALYSIS => 'Em Análise',
            self::IN_PROGRESS => 'Em Tratamento',
            self::RESOLVED => 'Resolvida',
            self::NOT_APPLICABLE => 'Não Aplicável',
        };
    }
}
