<x-table.table :headers="['Aluno', 'Curso / Série', 'Ano Letivo', 'Vigente', 'Ações']">
        @forelse($studentCourses as $enrollment)
            <tr>
                <x-table.td>{{ $enrollment->student->person->name }}</x-table.td>
                <x-table.td>{{ $enrollment->course->name }}</x-table.td>
                <x-table.td>{{ $enrollment->academic_year }}</x-table.td>

                <x-table.td>
                    @if($enrollment->is_current)
                        <span class="text-success" aria-label="Matrícula vigente">SIM</span>
                    @else
                        <span class="text-dark" aria-label="Matrícula não vigente">NÃO</span>
                    @endif
                </x-table.td>

                <x-table.td>
                    <x-table.actions>
                        <x-buttons.link-button 
                            :href="route('specialized-educational-support.student-courses.show', $enrollment)"
                            variant="info"
                            {{-- Melhorando a descrição: O leitor dirá exatamente QUEM o usuário vai ver --}}
                            aria-label="Visualizar curso {{ $enrollment->course->name }} de {{ $student->person->name }}"
                        >
                        <i class="fas fa-eye" aria-hidden="true"></i> ver
                        </x-buttons.link-button>

                        <form 
                            action="{{ route('specialized-educational-support.student-courses.destroy', $enrollment) }}" 
                            method="POST"
                            onsubmit="return confirm('Excluir esta matrícula do histórico?')"
                            aria-label="Excluir matrícula do aluno {{ $enrollment->student->person->name }}">
                            @csrf
                            @method('DELETE')

                            <x-buttons.submit-button variant="danger">
                                <i class="fas fa-trash" aria-hidden="true"></i> Excluir
                            </x-buttons.submit-button>
                        </form>
                    </x-table.actions>
                </x-table.td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center py-4 text-muted">
                    Nenhum curso alocado para esse aluno.
                </td>
            </tr>
        @endforelse
    </x-table.table>