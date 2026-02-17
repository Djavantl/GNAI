<x-table.table :headers="['Nome', 'Categoria', 'Relator', 'Prioridade', 'Status', 'Ações']">
    @forelse($barriers as $barrier)
        <tr>
            {{-- NOME --}}
            <x-table.td>{{ $barrier->name }}</x-table.td>

            {{-- CATEGORIA --}}
            <x-table.td>{{ $barrier->category?->name ?? '-' }}</x-table.td>

            {{-- RELATOR --}}
            <x-table.td>
                {{ $barrier->is_anonymous ? 'Anônimo' : ($barrier->registeredBy?->name ?? 'Sistema') }}
                @if($barrier->affected_person_role)
                    <small class="text-muted d-block">{{ $barrier->affected_person_role }}</small>
                @endif
            </x-table.td>

            {{-- PRIORIDADE --}}
            <x-table.td>
                <span class="text-{{ $barrier->priority?->color() ?? 'secondary' }} fw-bold">
                    {{ $barrier->priority?->label() ?? '-' }}
                </span>
            </x-table.td>

            {{-- STATUS --}}
            <x-table.td>
                @php $status = $barrier->latestStatus(); @endphp
                @if($status)
                    <span class="text-{{ $status->color() }} fw-bold">
                        {{ $status->label() }}
                    </span>
                @else
                    <span class="text-secondary fw-bold">Pendente</span>
                @endif
            </x-table.td>

            {{-- AÇÕES --}}
            <x-table.td>
                <x-table.actions>
                    <x-buttons.link-button
                        :href="route('inclusive-radar.barriers.show', $barrier)"
                        variant="info"
                    >
                        <i class="fas fa-eye"></i> Ver
                    </x-buttons.link-button>

                    <x-buttons.link-button
                        :href="route('inclusive-radar.barriers.edit', $barrier)"
                        variant="warning"
                    >
                        <i class="fas fa-edit"></i> Editar
                    </x-buttons.link-button>

                    <form action="{{ route('inclusive-radar.barriers.destroy', $barrier) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <x-buttons.submit-button
                            variant="danger"
                            onclick="return confirm('Deseja remover este relato?')"
                        >
                            <i class="fas fa-trash-alt"></i> Excluir
                        </x-buttons.submit-button>
                    </form>
                </x-table.actions>
            </x-table.td>
        </tr>
    @empty
        <tr>
            <td colspan="6" class="text-center text-muted py-4">Nenhuma barreira identificada até o momento.</td>
        </tr>
    @endforelse
</x-table.table>

@if(method_exists($barriers, 'hasPages') && $barriers->hasPages())
    <div class="mt-4">
        {{ $barriers->links() }}
    </div>
@endif
