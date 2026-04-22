<x-table.table
    :headers="[
        ['label' => 'Nome',        'responsive' => false],
        ['label' => 'Instituição', 'responsive' => true],
        ['label' => 'Tipo',        'responsive' => true],
        ['label' => 'Status',      'responsive' => true],
        ['label' => 'Ações',       'responsive' => false],
    ]"
    :records="$locations"
>
    @forelse($locations as $loc)
        <tr>
            <x-table.td :responsive="false">{{ $loc->name ?? 'N/A' }}</x-table.td>

            <x-table.td>{{ $loc->institution->name ?? 'N/A' }}</x-table.td>

            <x-table.td>{{ $loc->type ?? 'N/A' }}</x-table.td>

            <x-table.td>
                @php
                    $statusColor = $loc->is_active ? 'success' : 'secondary';
                    $statusLabel = $loc->is_active ? 'Ativo' : 'Inativo';
                @endphp

                <span class="text-{{ $statusColor }} fw-bold text-uppercase" style="font-size: 0.85rem;">
                    {{ $statusLabel }}
                </span>
            </x-table.td>

            <x-table.td :responsive="false">
                <x-table.actions>
                    @canany(['location.show', 'location.destroy'])
                        @can('location.show')
                            <x-buttons.link-button
                                :href="route('inclusive-radar.locations.show', $loc)"
                                variant="info"
                            >
                                <i class="fas fa-eye"></i> Ver
                            </x-buttons.link-button>
                        @endcan

                        @can('location.destroy')
                            <x-buttons.submit-button
                                type="button"
                                variant="danger"
                                data-bs-toggle="modal"
                                data-bs-target="#globalConfirmActionModal"
                                data-confirm-title="Excluir Ponto de Referencia"
                                data-confirm-message="O ponto {{ $loc->name }} sera excluido permanentemente."
                                data-confirm-action="{{ route('inclusive-radar.locations.destroy', $loc) }}"
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
            <td colspan="5" class="text-center text-muted py-4">
                Nenhum ponto de referência encontrado.
            </td>
        </tr>
    @endforelse
</x-table.table>
