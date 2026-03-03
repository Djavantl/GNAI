<x-table.table :headers="['Nome', 'Categoria', 'Relator', 'Prioridade', 'Status', 'Ações']" :records="$barriers">
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
                    $status = $barrier->latestStatus();
                    $statusColor = $status ? $status->color() : 'secondary';
                @endphp

                <span class="text-{{ $statusColor }} fw-bold text-uppercase" style="font-size: 0.85rem;">
                    {{ $status ? $status->label() : 'Pendente' }}
                </span>
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
    <div class="mt-4 px-3">
        {{ $barriers->links() }}
    </div>
@endif
