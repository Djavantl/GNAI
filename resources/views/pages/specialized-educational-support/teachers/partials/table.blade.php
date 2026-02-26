<x-table.table 
    :headers="['Nome', 'Email', 'Matrícula', 'Ações']" 
    :records="$teachers" 
    aria-label="Tabela de professores"
>
@forelse($teachers as $teacher)
    <tr>
        <x-table.td>
            <div class="name-with-photo">
                <img src="{{ $teacher->person->photo_url }}" 
                     class="avatar-table" 
                     alt="Foto de {{ $teacher->person->name }}">
                <span class="fw-bold text-purple-dark">{{ $teacher->person->name }}</span>
            </div>
        </x-table.td>
        
        <x-table.td>
            {{ $teacher->person->email }}
        </x-table.td>

        <x-table.td>
            {{ $teacher->registration }}
        </x-table.td>

        <x-table.td>
            <x-table.actions>
                <x-buttons.link-button 
                    :href="route('specialized-educational-support.teachers.show', $teacher)"
                    variant="info"
                    aria-label="Visualizar detalhes de {{ $teacher->person->name }}"
                >
                    <i class="fas fa-eye" aria-hidden="true"></i> ver
                </x-buttons.link-button>

                <form action="{{ route('specialized-educational-support.teachers.destroy', $teacher) }}"
                    method="POST"
                    class="d-inline">
                    @csrf
                    @method('DELETE')

                    <x-buttons.submit-button 
                        variant="danger"
                        onclick="return confirm('Deseja remover este professor? Todos os dados vinculados serão excluídos.')"
                        aria-label="Excluir professor {{ $teacher->person->name }}"
                    >
                       <i class="fas fa-trash" aria-hidden="true"></i> Excluir
                    </x-buttons.submit-button>
                </form>
            </x-table.actions>
        </x-table.td>
    </tr>
@empty
    <tr>
        <td colspan="4" class="text-center text-muted fw-bold py-5">
            <i class="fas fa-folder-open d-block mb-2" style="font-size: 2.5rem;"></i>
            Nenhum aluno encontrado.
        </td>
    </tr>
@endforelse
</x-table.table>