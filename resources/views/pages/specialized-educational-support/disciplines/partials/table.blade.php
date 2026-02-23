<x-table.table :headers="['Nome', 'Status', 'Ações']">
@foreach($disciplines as $discipline)
    <tr>
        <x-table.td>
            <span class="fw-bold text-purple-dark">{{ $discipline->name }}</span>
        </x-table.td>
        <x-table.td>
            <span class="text-{{ $discipline->is_active ? 'success' : 'danger' }} fw-bold">
                <i class="fas fa-{{ $discipline->is_active ? 'check' : 'times' }}-circle me-1"></i>
                {{ $discipline->is_active ? 'Ativo' : 'Inativo' }}
            </span>
        </x-table.td>
        <x-table.td>
            <x-table.actions>
                <x-buttons.link-button :href="route('specialized-educational-support.disciplines.show', $discipline)" variant="info">
                    <i class="fas fa-eye"></i> Ver
                </x-buttons.link-button>
                
                <form action="{{ route('specialized-educational-support.disciplines.destroy', $discipline) }}" method="POST">
                    @csrf @method('DELETE')
                    <x-buttons.submit-button variant="danger" onclick="return confirm('Deseja excluir esta disciplina?')">
                        <i class="fas fa-trash"></i> Excluir
                    </x-buttons.submit-button>
                </form>
            </x-table.actions>
        </x-table.td>
    </tr>
@endforeach
</x-table.table>