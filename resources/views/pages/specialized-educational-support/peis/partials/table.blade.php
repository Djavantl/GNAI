<x-table.table :headers="['Semestre', 'Curso', 'Status', 'Atual', 'Versão', 'Ações']"
:records="$peis">
        @forelse($peis as $pei)
            <tr>
                <x-table.td>
                    <strong>{{ $pei->semester->label ?? 'N/A' }}</strong><br>
                    <small class="text-muted">Criado em: {{ $pei->created_at->format('d/m/Y') }}</small>
                </x-table.td>

                <x-table.td>
                    <strong>{{ $pei->course->name ?? 'N/A' }}</strong><br>
                </x-table.td>

                <x-table.td>
                    @if($pei->is_finished)
                        <span class=" text-success ">
                            FINALIZADO
                        </span>
                    @else
                        <span class=" text-warning ">
                            EM ABERTO
                        </span>
                    @endif
                </x-table.td>

                 <x-table.td>
                    @if($pei->is_current)
                        <span class=" text-success ">
                            SIM
                        </span>
                    @else
                        <span class=" text-black">
                            NÃO
                        </span>
                    @endif
                </x-table.td>

                 <x-table.td>
                    <strong>V{{ $pei->version ?? 'N/A' }}</strong>
                </x-table.td>

                <x-table.td>
                    <x-table.actions>
                        @can('pei.view')
                        <x-buttons.link-button
                            :href="route('specialized-educational-support.pei.show', $pei->id)"
                            variant="info"
                        >
                            <i class="fas fa-eye" aria-hidden="true"></i> ver
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
                                aria-label="Excluir pei do sistema"
                            >
                            <i class="fas fa-trash" aria-hidden="true"></i> Excluir
                            </x-buttons.submit-button>
                        </form>
                        @endcan
                    </x-table.actions>
                </x-table.td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center text-muted py-5">
                    <i class="fas fa-folder-open d-block mb-2" style="font-size: 2rem;"></i>
                    Nenhum PEI do aluno encontrado no sistema.
                </td>
            </tr>
        @endforelse
    </x-table.table>
