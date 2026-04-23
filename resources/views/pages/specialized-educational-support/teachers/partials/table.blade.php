<x-table.table
    :headers="[
        ['label' => 'Nome',       'responsive' => false],
        ['label' => 'Email',      'responsive' => true],
        ['label' => 'Matrícula',  'responsive' => true],
        ['label' => 'Ações',      'responsive' => false],
    ]"
    :records="$teachers"
    aria-label="Tabela de professores"
>
    @forelse($teachers as $teacher)
        <tr>
            <x-table.td :responsive="false">
                <div class="name-with-photo">
                    <img src="{{ $teacher->person->photo_url }}"
                         class="avatar-table"
                         alt="Foto de {{ $teacher->person->name }}">
                    <span class="fw-bold text-purple-dark">
                    {{ $teacher->person->name }}
                </span>
                </div>
            </x-table.td>

            <x-table.td>{{ $teacher->person->email }}</x-table.td>

            <x-table.td>{{ $teacher->registration }}</x-table.td>

            <x-table.td :responsive="false">
                <x-table.actions>
                    @canany(['teacher.view', 'teacher.delete'])
                        @can('teacher.view')
                            <x-buttons.link-button
                                :href="route('specialized-educational-support.teachers.show', $teacher)"
                                variant="info"
                                aria-label="Visualizar detalhes de {{ $teacher->person->name }}"
                            >
                                <i class="fas fa-eye"></i> Ver
                            </x-buttons.link-button>
                        @endcan

                        @can('teacher.delete')
                            <x-buttons.submit-button
                                type="button"
                                variant="danger"
                                data-bs-toggle="modal"
                                data-bs-target="#globalConfirmActionModal"
                                data-confirm-title="Excluir Professor"
                                data-confirm-message="Deseja remover este professor? Todos os dados vinculados serao excluidos."
                                data-confirm-action="{{ route('specialized-educational-support.teachers.destroy', $teacher) }}"
                                data-confirm-method="DELETE"
                                data-confirm-submit-text="Confirmar Exclusao"
                                data-confirm-variant="danger"
                                aria-label="Excluir professor {{ $teacher->person->name }}"
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
            <td colspan="4" class="text-center text-muted py-4">
                Nenhum professor encontrado.
            </td>
        </tr>
    @endforelse
</x-table.table>
