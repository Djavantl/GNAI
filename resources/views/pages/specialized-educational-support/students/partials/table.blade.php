<x-table.table
    :headers="[
        ['label' => 'Nome',       'responsive' => false],
        ['label' => 'Contato',    'responsive' => true],
        ['label' => 'Matrícula',  'responsive' => true],
        ['label' => 'Status',     'responsive' => true],
        ['label' => 'Ingresso',   'responsive' => true],
        ['label' => 'Ações',      'responsive' => false],
    ]"
    :records="$students"
>
    @forelse($students as $student)
        <tr>
            <x-table.td :responsive="false">
                <div class="name-with-photo">
                    <img src="{{ $student->person->photo_url }}"
                         class="avatar-table"
                         alt="Foto de {{ $student->person->name }}">
                    <span class="fw-bold text-purple-dark">
                        {{ $student->person->name }}
                    </span>
                </div>
            </x-table.td>

            <x-table.td>{{ $student->person->email }}</x-table.td>

            <x-table.td>{{ $student->registration }}</x-table.td>

            <x-table.td>
                <span class="text-{{ $student->status->color() }} fw-bold text-uppercase"
                      style="font-size: 0.85rem;">
                    {{ $student->status->label() }}
                </span>
            </x-table.td>

            <x-table.td>{{ \Carbon\Carbon::parse($student->entry_date)->format('d/m/Y') }}</x-table.td>

            <x-table.td :responsive="false">
                <x-table.actions>
                    @canany(['student.view', 'student.delete'])
                        @can('student.view')
                            <x-buttons.link-button
                                :href="route('specialized-educational-support.students.show', $student)"
                                variant="info"
                                title="Ver ficha do aluno"
                            >
                                <i class="fas fa-eye"></i> Ver
                            </x-buttons.link-button>
                        @endcan

                        @can('student.delete')
                            <form action="{{ route('specialized-educational-support.students.destroy', $student) }}"
                                  method="POST"
                                  class="d-inline">
                                <x-buttons.submit-button
                                    type="button"
                                    variant="danger"
                                    data-bs-toggle="modal"
                                    data-bs-target="#globalConfirmActionModal"
                                    data-confirm-title="Excluir Aluno"
                                    data-confirm-message="Deseja realmente excluir este aluno?"
                                    data-confirm-action="{{ route('specialized-educational-support.students.destroy', $student) }}"
                                    data-confirm-method="DELETE"
                                    data-confirm-submit-text="Confirmar Exclusao"
                                    data-confirm-variant="danger"
                                >
                                    <i class="fas fa-trash"></i> Excluir
                                </x-buttons.submit-button>
                        @endcan
                    @else
                        <span class="text-purple-light">Nenhuma ação</span>
                    @endcanany
                </x-table.actions>
            </x-table.td>
        </tr>
    @empty
        <tr>
            <td colspan="6" class="text-center text-muted py-4">
                Nenhum aluno encontrado.
            </td>
        </tr>
    @endforelse
</x-table.table>
