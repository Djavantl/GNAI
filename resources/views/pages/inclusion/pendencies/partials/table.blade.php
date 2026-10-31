<x-table.table
    :headers="[
        ['label' => 'Título',       'responsive' => false],
        ['label' => 'Profissional', 'responsive' => true],
        ['label' => 'Prioridade',   'responsive' => true],
        ['label' => 'Vencimento',   'responsive' => true],
        ['label' => 'Concluída',    'responsive' => true],
        ['label' => 'Ações',        'responsive' => false],
    ]"
    :records="$pendencies"
>
    @forelse($pendencies as $pendency)
        <tr>
            <x-table.td :responsive="false">{{ $pendency->title }}</x-table.td>

            <x-table.td>{{ $pendency->assignedProfessional?->person?->name ?? '—' }}</x-table.td>

            <x-table.td>
                <span class="text-{{ $pendency->priority->color() }} fw-bold text-uppercase" style="font-size: 0.85rem;">
                    {{ $pendency->priority->label() }}
                </span>
            </x-table.td>

            <x-table.td>{{ $pendency->due_date?->format('d/m/Y') ?? '—' }}</x-table.td>

            <x-table.td>
                @php
                    $statusColor = $pendency->is_completed ? 'success' : 'danger';
                    $statusLabel = $pendency->is_completed ? 'Sim' : 'Não';
                    $statusIcon  = $pendency->is_completed ? 'check' : 'times';
                @endphp

                <span class="text-{{ $statusColor }} fw-bold text-uppercase" style="font-size: 0.85rem;">
                    <i class="fas fa-{{ $statusIcon }}-circle me-1"></i>
                    {{ $statusLabel }}
                </span>
            </x-table.td>

            <x-table.td :responsive="false">
                <x-table.actions>
                    @canany(['pendency.view', 'pendency.delete'])
                        @can('pendency.view')
                            <x-buttons.link-button
                                :href="route('specialized-educational-support.pendencies.show', $pendency)"
                                variant="info"
                            >
                                <i class="fas fa-eye"></i> Ver
                            </x-buttons.link-button>
                        @endcan

                        @can('pendency.delete')
                            <x-buttons.submit-button
                                type="button"
                                variant="danger"
                                data-bs-toggle="modal"
                                data-bs-target="#globalConfirmActionModal"
                                data-confirm-title="Excluir Pendencia"
                                data-confirm-message="Deseja excluir esta pendencia?"
                                data-confirm-action="{{ route('specialized-educational-support.pendencies.destroy', $pendency) }}"
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
            <td colspan="6" class="text-center text-muted py-4">
                Nenhuma pendência encontrada.
            </td>
        </tr>
    @endforelse
</x-table.table>
