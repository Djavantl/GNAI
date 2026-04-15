{{-- Tabela com Paginação --}}
<x-table.table
    :headers="['Nome','Contato', 'Matrícula', 'Status', 'Ingresso', 'Ações']"
    :records="$students" 
>
    @forelse($students as $student)
        <tr>
            <x-table.td>
                <div class="name-with-photo">
                    <img src="{{ $student->person->photo_url }}" class="avatar-table" alt="Foto">
                    <span class="fw-bold ">{{ $student->person->name }}</span>
                </div>
            </x-table.td>
            <x-table.td>{{ $student->person->email }}</x-table.td>
            <x-table.td>{{ $student->registration }}</x-table.td>
            <x-table.td >
                <span class="text-{{ $student->status->color() }} text-uppercase fw-bold">
                    {{ $student->status->label() }}
                </span>
            </x-table.td>
            <x-table.td>{{ \Carbon\Carbon::parse($student->entry_date)->format('d/m/Y') }}</x-table.td>

            <x-table.td>
                <x-table.actions>
                    @can('student.view')
                        <x-buttons.link-button
                            :href="route('specialized-educational-support.students.show', $student)"
                            variant="info"
                            title="Ver ficha do aluno"
                        >
                            <i class="fas fa-eye"></i>Ver
                        </x-buttons.link-button>
                    @endcan
                    @can('student.delete')
                        <form action="{{ route('specialized-educational-support.students.destroy', $student) }}"
                            method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <x-buttons.submit-button
                                variant="danger"
                                onclick="return confirm('Deseja realmente excluir este aluno?')"
                            >
                                <i class="fas fa-trash"></i>Excluir
                            </x-buttons.submit-button>
                        </form>
                    @endcan
                </x-table.actions>
            </x-table.td>
        </tr>
    @empty
        <tr>
            <td colspan="6" class="text-center text-muted fw-bold py-5">
                <i class="fas fa-folder-open d-block mb-2" style="font-size: 2.5rem;"></i>
                Nenhum aluno encontrado.
            </td>
        </tr>
    @endforelse
</x-table.table>
