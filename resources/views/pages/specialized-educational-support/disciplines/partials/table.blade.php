<x-table.table
    :headers="[
        ['label' => 'Nome',   'responsive' => false],
        ['label' => 'Status', 'responsive' => true],
        ['label' => 'Ações',  'responsive' => false],
    ]"
    :records="$disciplines"
>
    @forelse($disciplines as $discipline)
        <tr>
            <x-table.td :responsive="false">
                <span class="fw-bold text-purple-dark">{{ $discipline->name }}</span>
            </x-table.td>

            <x-table.td>
                @php
                    $statusColor = $discipline->is_active ? 'success' : 'danger';
                    $statusLabel = $discipline->is_active ? 'Ativo' : 'Inativo';
                    $statusIcon  = $discipline->is_active ? 'check' : 'times';
                @endphp

                <span class="text-{{ $statusColor }} fw-bold text-uppercase" style="font-size: 0.85rem;">
                    <i class="fas fa-{{ $statusIcon }}-circle me-1"></i>
                    {{ $statusLabel }}
                </span>
            </x-table.td>

            <x-table.td :responsive="false">
                <x-table.actions>
                    @canany(['discipline.view', 'discipline.delete'])
                        @can('discipline.view')
                            <x-buttons.link-button
                                :href="route('specialized-educational-support.disciplines.show', $discipline)"
                                variant="info"
                            >
                                <i class="fas fa-eye"></i> Ver
                            </x-buttons.link-button>
                        @endcan

                        @can('discipline.delete')
                            <x-buttons.submit-button
                                type="button"
                                variant="danger"
                                data-bs-toggle="modal"
                                data-bs-target="#globalConfirmActionModal"
                                data-confirm-title="Excluir Disciplina"
                                data-confirm-message="Deseja excluir esta disciplina?"
                                data-confirm-action="{{ route('specialized-educational-support.disciplines.destroy', $discipline) }}"
                                data-confirm-method="DELETE"
                                data-confirm-submit-text="Confirmar Exclusao"
                                data-confirm-variant="danger"
                            >
                                    <i class="fas fa-trash"></i> Excluir
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
                Nenhuma disciplina encontrada.
            </td>
        </tr>
    @endforelse
</x-table.table>
