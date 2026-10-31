<x-table.table
    :headers="[
        ['label' => 'Estudante', 'responsive' => false],
        ['label' => 'Semestre',  'responsive' => true],
        ['label' => 'Curso',     'responsive' => true],
        ['label' => 'Status',    'responsive' => true],
        ['label' => 'Versão',    'responsive' => true],
        ['label' => 'Ações',     'responsive' => false],
    ]"
    :records="$peis"
>
    @forelse($peis as $pei)
        <tr>
            <x-table.td :responsive="false">
                <div class="d-flex align-items-center">
                    <div>
                        <strong class="d-block">
                            {{ $pei->student->person->name }}
                        </strong>
                        <small class="text-muted">
                            Matrícula: {{ $pei->student->registration }}
                        </small>
                    </div>
                </div>
            </x-table.td>

            <x-table.td>
                <strong>{{ $pei->semester->label ?? 'N/A' }}</strong>
            </x-table.td>

            <x-table.td>
                <strong>{{ $pei->course->name ?? 'N/A' }}</strong>
            </x-table.td>

            <x-table.td>
                @php
                    $statusColor = $pei->is_finished ? 'success' : 'warning';
                    $statusLabel = $pei->is_finished ? 'Finalizado' : 'Em aberto';
                @endphp

                <span class="text-{{ $statusColor }} fw-bold text-uppercase" style="font-size: 0.85rem;">
                    {{ $statusLabel }}
                </span>
            </x-table.td>

            <x-table.td>
                <strong>V{{ $pei->version ?? 'N/A' }}</strong>
            </x-table.td>

            <x-table.td :responsive="false">
                <x-table.actions>
                    @canany(['pei.view', 'pei.delete'])

                        @can('pei.view')
                            <x-buttons.link-button
                                :href="route('specialized-educational-support.pei.show', $pei->id)"
                                variant="info"
                            >
                                <i class="fas fa-eye"></i> Ver
                            </x-buttons.link-button>
                        @endcan

                        @can('pei.delete')
                            <form action="{{ route('specialized-educational-support.pei.destroy', $pei) }}"
                                  method="POST"
                                  class="d-inline">
                                <x-buttons.submit-button
                                    type="button"
                                    variant="danger"
                                    data-bs-toggle="modal"
                                    data-bs-target="#globalConfirmActionModal"
                                    data-confirm-title="Excluir PEI"
                                    data-confirm-message="Deseja remover este PEI?"
                                    data-confirm-action="{{ route('specialized-educational-support.pei.destroy', $pei) }}"
                                    data-confirm-method="DELETE"
                                    data-confirm-submit-text="Confirmar Exclusao"
                                    data-confirm-variant="danger"
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
            <td colspan="6" class="text-center text-muted py-4">
                Nenhum PEI do aluno encontrado no sistema.
            </td>
        </tr>
    @endforelse
</x-table.table>
