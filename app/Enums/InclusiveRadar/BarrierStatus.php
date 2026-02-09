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

    public function color(): string {
        return match($this) {
            self::IDENTIFIED => 'secondary',
            self::UNDER_ANALYSIS => 'info',
            self::IN_PROGRESS => 'warning',
            self::RESOLVED => 'success',
            self::NOT_APPLICABLE => 'danger',
        };
    }

    public function allowsDeletion(): bool
    {
        return match($this) {
            self::RESOLVED,
            self::NOT_APPLICABLE => true,
            default => false,
        };
    }

}
