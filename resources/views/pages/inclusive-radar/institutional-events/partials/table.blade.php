<x-table.table :headers="['Nome', 'Data Inicial', 'Data Final', 'Horário', 'Status', 'Ações']" :records="$events">
    @forelse($events as $event)
        <tr>
            <x-table.td>{{ $event->title }}</x-table.td>

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

            <x-table.td>
                <x-table.actions>
                    <x-buttons.link-button
                        :href="route('inclusive-radar.institutional-events.show', $event)"
                        variant="info"
                    >
                        <i class="fas fa-eye"></i> Ver
                    </x-buttons.link-button>

                    <form action="{{ route('inclusive-radar.institutional-events.destroy', $event) }}"
                          method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <x-buttons.submit-button
                            variant="danger"
                            onclick="return confirm('Deseja remover este evento?')"
                        >
                            <i class="fas fa-trash-alt"></i> Excluir
                        </x-buttons.submit-button>
                    </form>
                </x-table.actions>
            </x-table.td>
        </tr>
    @empty
        <tr>
            <td colspan="6" class="text-center text-muted py-4">Nenhum evento cadastrado.</td>
        </tr>
    @endforelse
</x-table.table>
