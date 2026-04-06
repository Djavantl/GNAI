@props(['log'])

@php
    use Illuminate\Database\Eloquent\Relations\Relation;

    $modelClass = Relation::getMorphedModel($log->auditable_type) ?? $log->auditable_type;

    $fieldLabels = (class_exists($modelClass) && method_exists($modelClass, 'auditLabels'))
        ? $modelClass::auditLabels()
        : [];

    $oldValues = $log->old_values ?? [];
    $newValues = $log->new_values ?? [];
    $allFields = array_unique(array_merge(array_keys($oldValues), array_keys($newValues)));

    $formatValue = function ($field, $value) use ($modelClass) {
        if (is_null($value) || $value === '' || (is_array($value) && empty($value))) {
            return '—';
        }

        if (class_exists($modelClass) && method_exists($modelClass, 'auditFormatter')) {
            $formatted = (new ($modelClass::auditFormatter()))->format($field, $value);
            if ($formatted !== null) return $formatted;
        }

        if (is_bool($value)) return $value ? 'Sim' : 'Não';
        if (is_array($value)) return implode(', ', $value);

        return (string) $value;
    };
@endphp

<div class="log-change-list">
    @if($log->action === 'updated' && !empty($allFields))
        @foreach($allFields as $field)
            @continue(in_array($field, ['updated_at', 'created_at', 'deleted_at']))

            <div class="change-item">
                <div class="field-name">
                    {{ $fieldLabels[$field] ?? ucfirst(str_replace('_', ' ', $field)) }}
                </div>

                <div class="values-diff">
                    <span class="old-value">
                        {!! $formatValue($field, $oldValues[$field] ?? null) !!}
                    </span>

                    <i class="fas fa-long-arrow-alt-right diff-arrow"></i>

                    <span class="new-value">
                        {!! $formatValue($field, $newValues[$field] ?? null) !!}
                    </span>
                </div>
            </div>
        @endforeach

    @elseif($log->action === 'created')
        <div class="audit-note audit-note-info">
            Registro inicializado com os dados do sistema.
        </div>

    @elseif($log->action === 'deleted')
        <div class="audit-note audit-note-danger">
            Registro removido permanentemente.
        </div>

    @else
        <div class="text-muted small">—</div>
    @endif
</div>
