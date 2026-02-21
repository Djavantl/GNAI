<x-table.table :headers="['Nome', 'Status', 'Ações']" :records="$trainings">
    @forelse($trainings as $training)
        <tr>
            {{-- NOME --}}
            <x-table.td>{{ $training->title }}</x-table.td>

            {{-- STATUS --}}
            <x-table.td>
                <span class="text-{{ $training->is_active ? 'success' : 'secondary' }} fw-bold">
                    {{ $training->is_active ? 'Ativo' : 'Inativo' }}
                </span>
            </x-table.td>

            {{-- AÇÕES --}}
            <x-table.td>
                <x-table.actions>
                    <x-buttons.link-button
                        :href="route('inclusive-radar.trainings.show', $training)"
                        variant="info"
                    >
                        <i class="fas fa-eye"></i> Ver
                    </x-buttons.link-button>

                    <form action="{{ route('inclusive-radar.trainings.destroy', $training) }}"
                          method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <x-buttons.submit-button
                            variant="danger"
                            onclick="return confirm('Deseja remover este treinamento?')"
                        >
                            <i class="fas fa-trash-alt"></i> Excluir
                        </x-buttons.submit-button>
                    </form>
                </x-table.actions>
            </x-table.td>
        </tr>
    @empty
        <tr>
            <td colspan="3" class="text-center text-muted py-4">Nenhum treinamento cadastrado.</td>
        </tr>
    @endforelse
</x-table.table>
