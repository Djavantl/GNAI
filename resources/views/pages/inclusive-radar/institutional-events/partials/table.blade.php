<x-table.table
    :headers="[
        ['label' => 'Nome',         'responsive' => false],
        ['label' => 'Data Inicial', 'responsive' => true],
        ['label' => 'Data Final',   'responsive' => true],
        ['label' => 'Horário',      'responsive' => true],
        ['label' => 'Status',       'responsive' => true],
        ['label' => 'Ações',        'responsive' => false],
    ]"
    :records="$events"
>
    @forelse($events as $event)
        <tr>
            <x-table.td :responsive="false">{{ $event->title }}</x-table.td>

            <x-table.td>{{ $event->start_date->format('d/m/Y') }}</x-table.td>

            <x-table.td>{{ $event->end_date->format('d/m/Y') }}</x-table.td>

            <x-table.td>
                {{ \Carbon\Carbon::parse($event->start_time)->format('H:i') }}
                -
                {{ \Carbon\Carbon::parse($event->end_time)->format('H:i') }}
            </x-table.td>

            <x-table.td>
                <span class="text-{{ $event->is_active ? 'success' : 'secondary' }} fw-bold text-uppercase" style="font-size: 0.85rem;">
                    {{ $event->is_active ? 'Ativo' : 'Inativo' }}
                </span>
            </x-table.td>

            <x-table.td :responsive="false">
                <x-table.actions>
                    @canany(['institutional-event.show', 'institutional-event.destroy'])
                        @can('institutional-event.show')
                            <x-buttons.link-button
                                :href="route('inclusive-radar.institutional-events.show', $event)"
                                variant="info"
                            >
                                <i class="fas fa-eye"></i> Ver
                            </x-buttons.link-button>
                        @endcan

                        @can('institutional-event.destroy')
                            <x-buttons.submit-button
                                type="button"
                                variant="danger"
                                data-bs-toggle="modal"
                                data-bs-target="#globalConfirmActionModal"
                                data-confirm-title="Excluir Evento Institucional"
                                data-confirm-message="O evento {{ $event->title }} sera excluido permanentemente."
                                data-confirm-action="{{ route('inclusive-radar.institutional-events.destroy', $event) }}"
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
            <td colspan="6" class="text-center text-muted py-4">Nenhum evento cadastrado.</td>
        </tr>
    @endforelse
</x-table.table>
