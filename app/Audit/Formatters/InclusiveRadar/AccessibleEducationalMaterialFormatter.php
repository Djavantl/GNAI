<?php

namespace App\Audit\Formatters\InclusiveRadar;

use App\Audit\Formatters\AuditFormatter;
use App\Enums\InclusiveRadar\ConservationState;
use App\Enums\InclusiveRadar\ResourceStatus;
use App\Models\InclusiveRadar\AccessibilityFeature;
use App\Models\SpecializedEducationalSupport\Deficiency;

class AccessibleEducationalMaterialFormatter extends AuditFormatter
{
    protected function formatters(): array
    {
        return [
            'is_digital'             => fn($v) => $v ? 'Digital' : 'Físico',
            'is_active'              => fn($v) => $v ? 'Ativo' : 'Inativo',
            'is_loanable'            => fn($v) => $v ? 'Sim' : 'Não',
            'status'                 => ResourceStatus::class,
            'conservation_state'     => ConservationState::class,
            'deficiencies'           => fn($ids) => is_array($ids)
                ? Deficiency::whereIn('id', $ids)->pluck('name')->join(', ') ?: 'Nenhuma'
                : null,
            'accessibility_features' => fn($ids) => is_array($ids)
                ? AccessibilityFeature::whereIn('id', $ids)->pluck('name')->join(', ') ?: 'Nenhum'
                : null,
        ];
    }
}
