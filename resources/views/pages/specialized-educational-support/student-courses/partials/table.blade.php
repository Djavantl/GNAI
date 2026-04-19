<x-table.table
    :headers="[
        ['label' => 'Aluno',         'responsive' => false],
        ['label' => 'Curso / Série', 'responsive' => false],
        ['label' => 'Ano Letivo',    'responsive' => true],
        ['label' => 'Vigente',       'responsive' => true],
        ['label' => 'Ações',         'responsive' => false],
    ]"
    :records="$studentCourses"
>
    @forelse($studentCourses as $enrollment)
        <tr>
            <x-table.td :responsive="false">{{ $enrollment->student->person->name }}</x-table.td>

            <x-table.td :responsive="false">{{ $enrollment->course->name }}</x-table.td>

            <x-table.td>{{ $enrollment->academic_year }}</x-table.td>

            <x-table.td>
                @php
                    $statusColor = $enrollment->is_current ? 'success' : 'secondary';
                    $statusLabel = $enrollment->is_current ? 'Ativo' : 'Inativo';
                    $statusAria  = $enrollment->is_current ? 'Matrícula vigente' : 'Matrícula não vigente';
                @endphp

                <span class="text-{{ $statusColor }} fw-bold text-uppercase"
                      style="font-size: 0.85rem;"
                      aria-label="{{ $statusAria }}">
                    {{ $statusLabel }}
                </span>
            </x-table.td>

            <x-table.td :responsive="false">
                <x-table.actions>
                    @canany(['student-course.view', 'student-course.delete'])
                        @can('student-course.view')
                            <x-buttons.link-button
                                :href="route('specialized-educational-support.student-courses.show', $enrollment)"
                                variant="info"
                                aria-label="Visualizar curso {{ $enrollment->course->name }} de {{ $enrollment->student->person->name }}"
                            >
                                <i class="fas fa-eye"></i> Ver
                            </x-buttons.link-button>
                        @endcan

                        @can('student-course.delete')
                            <form
                                action="{{ route('specialized-educational-support.student-courses.destroy', $enrollment) }}"
                                method="POST"
                                class="d-inline"
                                onsubmit="return confirm('Excluir esta matrícula do histórico?')"
                            >
                                @csrf
                                @method('DELETE')

                                <x-buttons.submit-button
                                    variant="danger"
                                    aria-label="Excluir matrícula do aluno {{ $enrollment->student->person->name }}"
                                >
                                    <i class="fas fa-trash"></i> Excluir
                                </x-buttons.submit-button>
                            </form>
                        @endcan
                    @else
                        <span class="text-purple-light">Nenhuma ação</span>
                    @endcanany
                </x-table.actions>
            </x-table.td>
        </tr>
    @empty
        <tr>
            <td colspan="5" class="text-center text-muted py-4">
                Nenhum curso do aluno encontrado.
            </td>
        </tr>
    @endforelse
</x-table.table>
