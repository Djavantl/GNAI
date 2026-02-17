@props(['log'])

@php
    $actionConfig = match($log->action) {
        'created' => ['icon' => 'fas fa-plus', 'color' => 'success', 'label' => 'Criação'],
        'updated' => ['icon' => 'fas fa-pen', 'color' => 'warning', 'label' => 'Edição'],
        'deleted' => ['icon' => 'fas fa-trash', 'color' => 'danger', 'label' => 'Exclusão'],
        default => ['icon' => 'fas fa-history', 'color' => 'secondary', 'label' => $log->action],
    };
@endphp

<div class="log-entry d-flex border-bottom p-3 hover-bg-light">
    {{-- Lado Esquerdo: Ícone e Linha --}}
    <div class="d-flex flex-column align-items-center me-3" style="width: 40px;">
        <div class="log-icon-circle bg-{{ $actionConfig['color'] }} text-white">
            <i class="{{ $actionConfig['icon'] }} fa-xs"></i>
        </div>
        <div class="log-line-connector"></div>
    </div>

    {{-- Centro: Conteúdo --}}
    <div class="flex-grow-1">
        <div class="d-flex justify-content-between mb-1">
            <span class="fw-bold text-purple-dark">{{ $actionConfig['label'] }}</span>
            <span class="text-muted small"><i class="far fa-clock me-1"></i>{{ $log->created_at->format('d/m/Y H:i') }}</span>
        </div>

        <div class="log-details-area">
            <x-logs.detail-renderer :log="$log" />
        </div>

        <div class="mt-2 d-flex align-items-center gap-2">
            <div class="avatar-xs">
                <span class="avatar-title rounded-circle bg-light text-purple-dark border" style="font-size: 0.7rem; padding: 2px 6px;">
                    {{ strtoupper(substr($log->user?->name ?? 'S', 0, 1)) }}
                </span>
            </div>
            <small class="text-muted">Executado por: <strong>{{ $log->user?->name ?? 'Sistema' }}</strong></small>
        </div>
    </div>
</div>
