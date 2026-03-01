<x-table.table :headers="['Estudante', 'Semestre', 'Status', 'Ações']"
:records="$peis">
    @forelse($peis as $pei)
        <tr>
            <x-table.td>
                <div class="d-flex align-items-center">
                    
                    <div>
                        <strong class="d-block">{{ $pei->student->person->name }}</strong>
                        <small class="text-muted">Matrícula: {{ $pei->student->registration }}</small>
                    </div>
                </div>
            </x-table.td>

            <x-table.td>
                <strong>{{ $pei->semester->label ?? 'N/A' }}</strong>
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
                <x-table.actions>
                    <x-buttons.link-button
                        :href="route('specialized-educational-support.pei.show', $pei->id)"
                        variant="info"
                        title="Ver Detalhes"
                    >
                        <i class="fas fa-eye" aria-hidden="true"></i> Ver
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
            <td colspan="6" class="text-center text-muted py-5">
                <i class="fas fa-folder-open d-block mb-2" style="font-size: 2rem;"></i>
                Nenhum PEI do aluno encontrado no sistema.
            </td>
        </tr>
    @endforelse
</x-table.table>