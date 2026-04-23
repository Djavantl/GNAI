<x-table.table
    :headers="[
        ['label' => 'Nome do Curso', 'responsive' => false],
        ['label' => 'Disciplinas',   'responsive' => true],
        ['label' => 'Status',        'responsive' => true],
        ['label' => 'Ações',         'responsive' => false],
    ]"
    :records="$courses"
>
    @forelse($courses as $course)
        <tr>
            <x-table.td :responsive="false">{{ $course->name }}</x-table.td>

            <x-table.td>{{ $course->disciplines_count }} matérias</x-table.td>

            <x-table.td>
                @php
                    $statusColor = $course->is_active ? 'success' : 'danger';
                    $statusLabel = $course->is_active ? 'Ativo' : 'Inativo';
                @endphp

                <span class="text-{{ $statusColor }} fw-bold text-uppercase" style="font-size: 0.85rem;">
                    {{ $statusLabel }}
                </span>
            </x-table.td>

            <x-table.td :responsive="false">
                <x-table.actions>
                    @canany(['course.view', 'course.delete'])
                        @can('course.view')
                            <x-buttons.link-button
                                :href="route('specialized-educational-support.courses.show', $course)"
                                variant="info"
                            >
                                <i class="fas fa-eye"></i> Ver
                            </x-buttons.link-button>
                        @endcan

                        @can('course.delete')
                                <x-buttons.submit-button
                                    type="button"
                                    variant="danger"
                                    data-bs-toggle="modal"
                                    data-bs-target="#globalConfirmActionModal"
                                    data-confirm-title="Excluir Curso"
                                    data-confirm-message="Excluir este curso?"
                                    data-confirm-action="{{ route('specialized-educational-support.courses.destroy', $course) }}"
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
            <td colspan="4" class="text-center text-muted py-4">
                Nenhum curso encontrado.
            </td>
        </tr>
    @endforelse
</x-table.table>
