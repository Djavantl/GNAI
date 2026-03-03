<x-table.table :headers="['Nome', 'Instituição', 'Tipo', 'Status', 'Ações']" :records="$locations">
    @forelse($locations as $loc)
        <tr>
            {{-- NOME --}}
            <x-table.td>{{ $loc->name ?? 'N/A' }}</x-table.td>

            {{-- INSTITUIÇÃO --}}
            <x-table.td>{{ $loc->institution->name ?? 'N/A' }}</x-table.td>

            {{-- TIPO --}}
            <x-table.td>{{ $loc->type ?? 'N/A' }}</x-table.td>

            <x-table.td>
                @php
                    $statusColor = $loc->is_active ? 'success' : 'secondary';
                    $statusLabel = $loc->is_active ? 'Ativo' : 'Inativo';
                @endphp

                {{-- Status da localização seguindo o padrão Clean & Bold do sistema --}}
                <span class="text-{{ $statusColor }} fw-bold text-uppercase" style="font-size: 0.85rem;">
                    {{ $statusLabel }}
                </span>
            </x-table.td>

            {{-- AÇÕES --}}
            <x-table.td>
                <x-table.actions>
                    <x-buttons.link-button
                        :href="route('inclusive-radar.locations.show', $loc)"
                        variant="info"
                    >
                        <i class="fas fa-eye"></i> Ver
                    </x-buttons.link-button>

                    <form action="{{ route('inclusive-radar.locations.destroy', $loc) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <x-buttons.submit-button
                            variant="danger"
                            onclick="return confirm('Deseja remover este ponto de referência?')"
                        >
                            <i class="fas fa-trash-alt"></i> Excluir
                        </x-buttons.submit-button>
                    </form>
                </x-table.actions>
            </x-table.td>
        </tr>
    @empty
        <tr>
            <td colspan="5" class="text-center text-muted py-4">Nenhum ponto de referência encontrado.</td>
        </tr>
    @endforelse
</x-table.table>

