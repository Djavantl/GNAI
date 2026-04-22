<x-table.table
    :headers="[
        ['label' => 'Nome',        'responsive' => false],
        ['label' => 'Localização', 'responsive' => true],
        ['label' => 'Status',      'responsive' => true],
        ['label' => 'Ações',       'responsive' => false],
    ]"
    :records="$institutions"
>
    @forelse($institutions as $inst)
        <tr>
            <x-table.td :responsive="false">
                {{ $inst->name }}
            </x-table.td>

            <x-table.td>
                {{ $inst->city }} - {{ $inst->state }}
            </x-table.td>

            <x-table.td>
                @php
                    $statusColor = $inst->is_active ? 'success' : 'danger';
                    $statusLabel = $inst->is_active ? 'Ativo' : 'Inativo';
                @endphp
                <span class="text-{{ $statusColor }} fw-bold text-uppercase" style="font-size: 0.85rem;">
                    {{ $statusLabel }}
                </span>
            </x-table.td>

            <x-table.td :responsive="false">
                <x-table.actions>
                    @canany(['institution.show', 'institution.destroy'])
                        @can('institution.show')
                            <x-buttons.link-button
                                :href="route('inclusive-radar.institutions.show', $inst)"
                                variant="info"
                            >
                                <i class="fas fa-eye"></i> Ver
                            </x-buttons.link-button>
                        @endcan
                        @can('institution.destroy')
                            <x-buttons.submit-button
                                type="button"
                                variant="danger"
                                data-bs-toggle="modal"
                                data-bs-target="#globalConfirmActionModal"
                                data-confirm-title="Excluir Instituicao"
                                data-confirm-message="A instituicao {{ $inst->name }} sera excluida permanentemente."
                                data-confirm-action="{{ route('inclusive-radar.institutions.destroy', $inst) }}"
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
            <td colspan="4" class="text-center text-muted py-4">
                Nenhuma instituição cadastrada.
            </td>
        </tr>
    @endforelse
</x-table.table>
