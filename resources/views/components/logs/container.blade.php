@props(['logs'])

<div class="custom-table-card overflow-hidden">
    <div class="bg-white p-4 border-bottom d-flex justify-content-between align-items-center">
        <h5 class="mb-0 text-purple-dark fw-bold">Histórico de Alterações</h5>
        <span class="badge bg-purple px-3">{{ $logs->total() }} Registros</span>
    </div>

    <div class="log-timeline-container p-0">
        @forelse($logs as $log)
            <x-logs.item :log="$log" />
        @empty
            <div class="p-5 text-center text-muted">
                <i class="fas fa-clipboard-list fa-3x mb-3 opacity-25"></i>
                <p>Nenhum registro de auditoria encontrado.</p>
            </div>
        @endforelse
    </div>

    @if($logs->hasPages())
        <div class="p-3 border-top bg-light">
            {{ $logs->links() }}
        </div>
    @endif
</div>
