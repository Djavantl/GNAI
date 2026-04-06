<?php

namespace App\Audit\Formatters\InclusiveRadar;

use App\Audit\Formatters\AuditFormatter;
use App\Enums\InclusiveRadar\ConservationState;
use App\Enums\InclusiveRadar\ResourceStatus;
use App\Models\SpecializedEducationalSupport\Deficiency;

class AssistiveTechnologyFormatter extends AuditFormatter
{
    protected function formatters(): array
    {
        return [
            'is_digital'         => fn($v) => $v ? 'Digital' : 'Físico',
            'is_active'          => fn($v) => $v ? 'Ativo' : 'Inativo',
            'is_loanable'        => fn($v) => $v ? 'Sim' : 'Não',
            'status'             => ResourceStatus::class,
            'conservation_state' => ConservationState::class,
            'deficiencies'       => fn($ids) => is_array($ids)
                ? Deficiency::whereIn('id', $ids)->pluck('name')->join(', ') ?: 'Nenhuma'
                : null,
        ];
    }
}
