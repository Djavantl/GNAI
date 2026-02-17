<x-table.table
    :headers="['Nome', 'Status', 'Ações']"
    :records="$features"
>
    @forelse($features as $feature)
        <tr>
            {{-- NOME --}}
            <x-table.td>
                {{ $feature->name }}
            </x-table.td>

            {{-- STATUS --}}
            <x-table.td>
                <span class="text-{{ $feature->is_active ? 'success' : 'secondary' }} fw-bold">
                    {{ $feature->is_active ? 'Ativo' : 'Inativo' }}
                </span>
            </x-table.td>

            {{-- AÇÕES --}}
            <x-table.td>
                <x-table.actions>

                    <x-buttons.link-button
                        :href="route('inclusive-radar.accessibility-features.show', $feature)"
                        variant="info"
                    >
                        <i class="fas fa-eye"></i> Ver
                    </x-buttons.link-button>

                    <x-buttons.link-button
                        :href="route('inclusive-radar.accessibility-features.edit', $feature)"
                        variant="warning"
                    >
                        <i class="fas fa-edit"></i> Editar
                    </x-buttons.link-button>

                    <form action="{{ route('inclusive-radar.accessibility-features.destroy', $feature) }}"
                          method="POST"
                          class="d-inline">
                        @csrf
                        @method('DELETE')

                        <x-buttons.submit-button
                            variant="danger"
                            onclick="return confirm('Deseja realmente remover este recurso?')"
                        >
                            <i class="fas fa-trash-alt"></i> Excluir
                        </x-buttons.submit-button>
                    </form>

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
