@props(['log'])

@php
    $actionConfig = match($log->action) {
        'created' => [
            'icon' => 'fas fa-plus',
            'color' => 'success',
            'label' => 'Criação'
        ],
        'updated' => [
            'icon' => 'fas fa-pen',
            'color' => 'purple',
            'label' => 'Edição'
        ],
        'deleted' => [
            'icon' => 'fas fa-trash',
            'color' => 'danger',
            'label' => 'Exclusão'
        ],
        default => [
            'icon' => 'fas fa-history',
            'color' => 'secondary',
            'label' => $log->action
        ],
    };
@endphp

<div class="log-item">
    <div class="log-marker-column">
        <div class="log-marker bg-{{ $actionConfig['color'] }}">
            <i class="{{ $actionConfig['icon'] }}"></i>
        </div>
        <div class="log-connector"></div>
    </div>

    <div class="log-card">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
            <div>
                <span class="log-action-badge bg-{{ $actionConfig['color'] }}">
                    {{ $actionConfig['label'] }}
                </span>
            </div>

            <div class="text-muted small">
                <i class="far fa-clock me-1"></i>
                {{ $log->created_at->format('d/m/Y H:i') }}
            </div>
        </div>

        <div class="log-details-area">
            <x-logs.detail-renderer :log="$log" />
        </div>

        <div class="log-author mt-3 pt-3">
            <div class="avatar-xs me-2">
                <span class="avatar-title rounded-circle">
                    {{ strtoupper(substr($log->user?->name ?? 'S', 0, 1)) }}
                </span>
            </div>

            <small class="text-muted">
                Executado por: <strong>{{ $log->user?->name ?? 'Sistema' }}</strong>
            </small>
        </div>
    </div>
</div>
