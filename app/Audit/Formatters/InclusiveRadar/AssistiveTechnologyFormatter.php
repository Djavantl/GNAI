<?php

namespace App\Audit\Formatters\InclusiveRadar;

use App\Audit\Formatters\AuditFormatter;
use App\Domains\InclusiveRadar\Domain\Enums\ConservationState;
use App\Domains\InclusiveRadar\Domain\Enums\ResourceStatus;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Deficiency;

class AssistiveTechnologyFormatter extends AuditFormatter
{
    protected function formatters(): array
    {
        return [
            'is_digital' => fn ($v) => $v ? 'Digital' : 'Físico',
            'is_active' => fn ($v) => $v ? 'Ativo' : 'Inativo',
            'is_loanable' => fn ($v) => $v ? 'Sim' : 'Não',
            'status' => ResourceStatus::class,
            'conservation_state' => ConservationState::class,
            'deficiencies' => fn ($ids) => is_array($ids)
                ? Deficiency::whereIn('id', $ids)->pluck('name')->join(', ') ?: 'Nenhuma'
                : null,
        ];
    }
}
