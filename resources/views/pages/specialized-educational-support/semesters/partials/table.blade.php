<x-table.table :headers="['Ano / Período', 'Rótulo Identificador', 'Status', 'Ações']">
        @forelse($semesters as $semester)
            <tr class="{{ $semester->is_current ? 'table-primary' : '' }}">
                <x-table.td>
                    <div class="fw-bold">{{ $semester->year }}</div>
                    <small class="text-muted text-uppercase">{{ $semester->term }}º Período</small>
                </x-table.td>

                <x-table.td>
                    <span class="text-uppercase fw-bold">{{ $semester->label }}</span>
                </x-table.td>

                <x-table.td >
                    @if($semester->is_current)
                        <span class="text-success">
                            SEMESTRE ATUAL
                        </span>
                    @else
                        <span class="text-muted">Histórico</span>
                    @endif
                </x-table.td>

                <x-table.td>
                    <x-table.actions>

                        <x-buttons.link-button
                            :href="route('specialized-educational-support.semesters.show', $semester)"
                            variant="info"
                        >
                            <i class="fas fa-eye"></i>ver
                        </x-buttons.link-button>

                        <x-buttons.link-button
                            :href="route('specialized-educational-support.semesters.edit', $semester)"
                            variant="warning"
                        >
                           <i class="fas fa-edit"></i> Editar
                        </x-buttons.link-button>

                        @if(!$semester->is_current)
                            <form action="{{ route('specialized-educational-support.semesters.setCurrent', $semester) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <x-buttons.submit-button variant="success">
                                    <i class="fas fa-check"></i>Definir Atual
                                </x-buttons.submit-button>
                            </form>
                        @endif

                        <form action="{{ route('specialized-educational-support.semesters.destroy', $semester) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <x-buttons.submit-button
                                variant="danger"
                                onclick="return confirm('Tem certeza que deseja excluir este semestre?')"
                            >
                                <i class="fas fa-trash"></i>Excluir
                            </x-buttons.submit-button>
                        </form>
                    </x-table.actions>
                </x-table.td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center text-muted py-5">
                    Nenhum semestre cadastrado.
                </td>
            </tr>
        @endforelse
    </x-table.table>