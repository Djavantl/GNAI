<x-table.table :headers="['Cargo', 'Ativo', 'Ações']">
        @foreach($positions as $item)
            <tr> 
                <x-table.td><strong>{{ $item->name }}</strong></x-table.td>

                <x-table.td>
                    @if($item->is_active)
                        <span class="text-success font-weight-bold">SIM
                    @else
                        <span class="text-danger font-weight-bold">NÃO</span>
                    @endif
                </x-table.td>

                <x-table.td>
                    <x-table.actions>

                        <x-buttons.link-button
                            :href="route('specialized-educational-support.positions.show', $item)"
                            variant="info"
                        >
                            <i class="fas fa-eye"></i>ver
                        </x-buttons.link-button>

                        <form action="{{ route('specialized-educational-support.positions.deactivate', $item) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <x-buttons.submit-button variant="dark">
                               <i class="fas fa-check"></i> Ativar/Desativar
                            </x-buttons.submit-button>
                        </form>

                        <form action="{{ route('specialized-educational-support.positions.destroy', $item) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <x-buttons.submit-button
                                variant="danger"
                                onclick="return confirm('Deseja excluir este cargo?')"
                            >
                                <i class="fas fa-trash"></i>Excluir
                            </x-buttons.submit-button>
                        </form>
                    </x-table.actions>
                </x-table.td>
            </tr>
        @endforeach
    </x-table.table>