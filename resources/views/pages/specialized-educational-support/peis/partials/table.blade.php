<x-table.table :headers="['Semestre', 'Status', 'Ações']"
:records="$peis">
        @forelse($peis as $pei)
            <tr>
                <x-table.td>
                    <strong>{{ $pei->semester->label ?? 'N/A' }}</strong><br>
                    <small class="text-muted">Criado em: {{ $pei->created_at->format('d/m/Y') }}</small>
                </x-table.td>

                <x-table.td>
                    @if($pei->is_finished)
                        <span class="badge bg-success">
                            <i class="fas fa-check-circle me-1"></i> Finalizado
                        </span>
                    @else
                        <span class="badge bg-warning text-dark">
                            <i class="fas fa-clock me-1"></i> Em andamento
                        </span>
                    @endif
                </x-table.td>

                <x-table.td>
                    <x-table.actions>
                        <x-buttons.link-button
                            :href="route('specialized-educational-support.pei.show', $pei->id)"
                            variant="info"
                        >
                            <i class="fas fa-eye" aria-hidden="true"></i> ver
                        </x-buttons.link-button>
                        <form action="{{ route('specialized-educational-support.pei.destroy', $pei) }}"
                            method="POST"
                            class="d-inline">
                            @csrf
                            @method('DELETE')

                            <x-buttons.submit-button 
                                variant="danger"
                                onclick="return confirm('Deseja remover este pei?')"
                                aria-label="Excluir pei do sistema"
                            >
                            <i class="fas fa-trash" aria-hidden="true"></i> Excluir
                            </x-buttons.submit-button>
                        </form>
                    </x-table.actions>
                </x-table.td>
            </tr> 
        @empty
            <tr>
                <td colspan="5" class="text-center text-muted py-5">
                    <i class="fas fa-folder-open d-block mb-2" style="font-size: 2rem;"></i>
                    Nenhum PEI do aluno encontrado no sistema.
                </td>
            </tr>
        @endforelse
    </x-table.table>