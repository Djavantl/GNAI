<x-table.table
    :headers="[
        ['label' => 'Nome',       'responsive' => false],
        ['label' => 'Categoria',  'responsive' => true],
        ['label' => 'Prioridade', 'responsive' => true],
        ['label' => 'Status',     'responsive' => true],
        ['label' => 'Ações',      'responsive' => false],
    ]"
    :records="$barriers"
>
    @forelse($barriers as $barrier)
        <tr>
            <x-table.td :responsive="false">{{ $barrier->name }}</x-table.td>

            <x-table.td>{{ $barrier->category?->name ?? '-' }}</x-table.td>

            <x-table.td>
                @php
                    $prioColor = $barrier->priority?->color() ?? 'secondary';
                @endphp
                <span class="text-{{ $prioColor }} fw-bold text-uppercase" style="font-size: 0.85rem;">
                    {{ $barrier->priority?->label() ?? '-' }}
                </span>
            </x-table.td>

            <x-table.td>
                @php
                    $status = $barrier->inspections->first()?->status;
                    $statusColor = $status ? $status->color() : 'secondary';
                @endphp
                <span class="text-{{ $statusColor }} fw-bold text-uppercase" style="font-size: 0.85rem;">
                    {{ $status ? $status->label() : 'Pendente' }}
                </span>
            </x-table.td>

            <x-table.td :responsive="false">
                <x-table.actions>
                    @canany(['barrier.show', 'barrier.destroy'])
                        @can('barrier.show')
                            <x-buttons.link-button
                                :href="route('inclusive-radar.barriers.show', $barrier)"
                                variant="info"
                            >
                                <i class="fas fa-eye"></i> Ver
                            </x-buttons.link-button>
                        @endcan
                        @can('barrier.destroy')
                            <x-buttons.submit-button
                                type="button"
                                variant="danger"
                                data-bs-toggle="modal"
                                data-bs-target="#globalConfirmActionModal"
                                data-confirm-title="Excluir Barreira"
                                data-confirm-message="O registro {{ $barrier->name }} sera excluido permanentemente."
                                data-confirm-action="{{ route('inclusive-radar.barriers.destroy', $barrier) }}"
                                data-confirm-method="DELETE"
                                data-confirm-submit-text="Confirmar Exclusao"
                                data-confirm-variant="danger"
                            >
                                <i class="fas fa-trash-alt"></i> Excluir
                            </x-buttons.submit-button>
                        @endcan
                    @else
                        <span class="text-purple-light">Nenhuma ação</span>
                    @endcanany
                </x-table.actions>
            </x-table.td>
        </tr>
    @empty
        <tr>
            <td colspan="5" class="text-center text-muted py-4">Nenhuma barreira identificada até o momento.</td>
        </tr>
    @endforelse
</x-table.table>

@if(method_exists($barriers, 'hasPages') && $barriers->hasPages())
    <div class="mt-4 px-3">
        {{ $barriers->links() }}
    </div>
@endif
