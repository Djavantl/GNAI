<x-table.table
    :headers="[
        ['label' => 'Nome',   'responsive' => false],
        ['label' => 'Status', 'responsive' => true],
        ['label' => 'Ações',  'responsive' => false],
    ]"
    :records="$features"
>
    @forelse($features as $feature)
        <tr>
            <x-table.td :responsive="false">
                {{ $feature->name }}
            </x-table.td>

            <x-table.td>
                <span class="text-{{ $feature->is_active ? 'success' : 'secondary' }} fw-bold text-uppercase" style="font-size: 0.85rem;">
                    {{ $feature->is_active ? 'Ativo' : 'Inativo' }}
                </span>
            </x-table.td>

            <x-table.td :responsive="false">
                <x-table.actions>
                    @canany(['accessibility-feature.show', 'accessibility-feature.destroy'])
                        @can('accessibility-feature.store')
                            <x-buttons.link-button
                                :href="route('inclusive-radar.accessibility-features.show', $feature)"
                                variant="info"
                            >
                                <i class="fas fa-eye"></i> Ver
                            </x-buttons.link-button>
                        @endcan
                        @can('accessibility-feature.destroy')
                            <x-buttons.submit-button
                                type="button"
                                variant="danger"
                                data-bs-toggle="modal"
                                data-bs-target="#globalConfirmActionModal"
                                data-confirm-title="Excluir Recurso de Acessibilidade"
                                data-confirm-message="O recurso {{ $feature->name }} sera excluido permanentemente."
                                data-confirm-action="{{ route('inclusive-radar.accessibility-features.destroy', $feature) }}"
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
            <td colspan="3" class="text-center text-muted py-4">
                Nenhum recurso de acessibilidade cadastrado.
            </td>
        </tr>
    @endforelse
</x-table.table>
