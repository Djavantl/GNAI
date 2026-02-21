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
                    <span class="fw-bold text-purple-dark">{{ $student->person->name }}</span>
                </div>
            </x-table.td>
            <x-table.td>{{ $student->person->email }}</x-table.td>
            <x-table.td>{{ $student->registration }}</x-table.td>
            <x-table.td >
                @php
                    $statusColor = $student->status === 'active' ? 'success' : 'danger';
                    $statusLabel = $student->status === 'active' ? 'Ativo' : 'Inativo';
                @endphp
                <span class="text-{{ $statusColor }} text-uppercase fw-bold">
                    {{ $statusLabel }}
                </span>
            </x-table.td>
            <x-table.td>{{ \Carbon\Carbon::parse($student->entry_date)->format('d/m/Y') }}</x-table.td>

            <x-table.td>
                <x-table.actions>
                    <x-buttons.link-button
                        :href="route('specialized-educational-support.students.show', $student)"
                        variant="info"
                        title="Ver ficha do aluno"
                    >
                        <i class="fas fa-eye"></i>Ver
                    </x-buttons.link-button>

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
                </x-table.actions>
            </x-table.td>
        </tr>
    @empty
        <tr>
            <td colspan="6" class="text-center py-4 text-muted">
                Nenhum aluno encontrado para a busca realizada.
            </td>
        </tr>
    @endforelse
</x-table.table>
