@extends('layouts.master')

@section('title', 'Cargos')

@section('content')
    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Lista de Cargos</h2>
            <p class="text-muted">Gerenciamento de funções para o suporte especializado.</p>
        </div>
        <x-buttons.link-button
            :href="route('specialized-educational-support.positions.create')"
            variant="new"
        >
            Novo Cargo
        </x-buttons.link-button>
    </div>

    <x-table.table :headers="['Cargo', 'Ativo', 'Ações']">
        @foreach($position as $item)
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
                            variant="info-outline"
                        >
                            ver
                        </x-buttons.link-button>

                        <x-buttons.link-button
                            :href="route('specialized-educational-support.positions.edit', $item)"
                            variant="warning-outline"
                        >
                            Editar
                        </x-buttons.link-button>

                        <form action="{{ route('specialized-educational-support.positions.deactivate', $item) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <x-buttons.submit-button variant="dark-outline">
                                Ativar/Desativar
                            </x-buttons.submit-button>
                        </form>

                        <form action="{{ route('specialized-educational-support.positions.destroy', $item) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <x-buttons.submit-button
                                variant="danger"
                                onclick="return confirm('Deseja excluir este cargo?')"
                            >
                                Excluir
                            </x-buttons.submit-button>
                        </form>
                    </x-table.actions>
                </x-table.td>
            </tr>
        @endforeach
    </x-table.table>
@endsection