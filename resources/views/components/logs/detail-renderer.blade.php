@props(['log'])

@php
    use Illuminate\Database\Eloquent\Relations\Relation;

    // 1. Resolve o nome da classe real (Ex: transforma 'assistive_technology' em 'App\Models\...\AssistiveTechnology')
    $modelClass = Relation::getMorphedModel($log->auditable_type) ?? $log->auditable_type;

    $globalLabels = [
        'name' => 'Nome',
        'description' => 'Descrição',
        'is_active' => 'Ativo',
        'status_id' => 'Status',
        'type_id' => 'Tipo',
    ];

    // 2. Busca labels do Model
    $modelLabels = [];
    if (class_exists($modelClass) && method_exists($modelClass, 'getAuditLabels')) {
        $modelLabels = $modelClass::getAuditLabels();
    }

    $fieldLabels = array_merge($globalLabels, $modelLabels);

    $oldValues = $log->old_values ?? [];
    $newValues = $log->new_values ?? [];

    $formatValue = function ($field, $value, $log) use ($modelClass) {
        if (is_null($value) || $value === '' || (is_array($value) && empty($value))) return '—';

        // 3. Tenta formatar pelo Model primeiro
        if (class_exists($modelClass) && method_exists($modelClass, 'formatAuditValue')) {
            $formatted = $modelClass::formatAuditValue($field, $value);
            if ($formatted !== null) return $formatted;
        }

        // Fallbacks globais
        if (is_bool($value) || (in_array($field, ['is_active', 'requires_training']) && ($value === '1' || $value === '0'))) {
            return ($value == '1' || $value === true) ? 'Sim' : 'Não';
        }

        if (is_array($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE);
        }

        return (string) $value;
    };

    $allFields = array_unique(array_merge(array_keys($oldValues), array_keys($newValues)));
@endphp

<div class="log-change-list">
    @if($log->action === 'updated' && !empty($allFields))
        @foreach($allFields as $field)
            @continue(in_array($field, ['updated_at', 'created_at', 'deleted_at']))

            <div class="change-item mb-2 border-bottom pb-1">
                <div class="field-name fw-bold text-muted small uppercase" style="font-size: 0.7rem;">
                    {{ $fieldLabels[$field] ?? ucfirst(str_replace('_',' ',$field)) }}
                </div>

                <div class="values-diff d-flex align-items-center flex-wrap">
                    <span class="old-value text-danger text-decoration-line-through me-2 small">
                        {!! $formatValue($field, $oldValues[$field] ?? null, $log) !!}
                    </span>

                    <i class="fas fa-long-arrow-alt-right mx-2 text-muted opacity-50"></i>

                    <span class="new-value text-success fw-bold ms-2 small">
                        {!! $formatValue($field, $newValues[$field] ?? null, $log) !!}
                    </span>
                </div>
            </div>
        @endforeach
    @else
        <div class="alert alert-{{ $log->action === 'created' ? 'info' : 'danger' }} py-1 px-2 mb-0 small">
            {{ $log->action === 'created' ? 'Registro inicializado com os dados acima.' : 'Registro removido permanentemente.' }}
        </div>
    @endif
</div>
