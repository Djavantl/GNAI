@props(['logs'])

<div class="log-timeline-wrapper p-4 p-lg-5">
    @forelse($logs as $log)
        <x-logs.item :log="$log" />
    @empty
        <div class="empty-history text-center py-5">
            <div class="empty-history-icon mb-3">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <h6 class="mb-1 text-purple-dark">Nenhum registro encontrado</h6>
            <p class="text-muted mb-0">Não há alterações registradas para este item.</p>
        </div>
        @endforelse

    @if($logs->hasPages())
        <div class="pt-4 mt-4 border-top">
            {{ $logs->links() }}
        </div>
    @endif
</div>
