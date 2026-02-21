<x-table.table :headers="['Rótulo / Nome', 'Obrigatório', 'Status', 'Ações']" :records="$attributes">
    @forelse($attributes as $attr)
        <tr>
            {{-- RÓTULO / NOME --}}
            <x-table.td>
                <span class="fw-bold">{{ $attr->label ?? 'N/A' }}</span>
                <small class="text-muted d-block">{{ $attr->name }}</small>
            </x-table.td>

            {{-- OBRIGATÓRIO --}}
            <x-table.td>
                @if($attr->is_required)
                    <span class="text-warning fw-bold">Sim</span>
                @else
                    <span class="text-muted">Não</span>
                @endif
            </x-table.td>

            {{-- STATUS --}}
            <x-table.td>
                @php
                    $statusColor = $attr->is_active ? 'success' : 'secondary';
                    $statusLabel = $attr->is_active ? 'Ativo' : 'Inativo';
                @endphp
                <span class="text-{{ $statusColor }} fw-bold">
                    {{ $statusLabel }}
                </span>
            </x-table.td>

            {{-- AÇÕES --}}
            <x-table.td>
                <x-table.actions>
                    <x-buttons.link-button
                        :href="route('inclusive-radar.type-attributes.show', $attr)"
                        variant="info"
                    >
                        <i class="fas fa-eye"></i> Ver
                    </x-buttons.link-button>

                    <form action="{{ route('inclusive-radar.type-attributes.destroy', $attr) }}"
                          method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <x-buttons.submit-button
                            variant="danger"
                            onclick="return confirm('Tem certeza que deseja remover este atributo?')"
                        >
                            <i class="fas fa-trash-alt"></i> Excluir
                        </x-buttons.submit-button>
                    </form>
                </x-table.actions>
            </x-table.td>
        </tr>
    @empty
        <tr>
            <td colspan="4" class="text-center text-muted py-4">Nenhum atributo personalizado cadastrado.</td>
        </tr>
    @endforelse
</x-table.table>
