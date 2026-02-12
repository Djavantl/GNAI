@extends('layouts.master')

@section('title', 'Deficiências')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Deficiências' => null
        ]" />
    </div>
    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Lista de Deficiências</h2>
            <p class="text-muted">Categorias e códigos CID registrados.</p>
        </div>
        <x-buttons.link-button
            :href="route('specialized-educational-support.deficiencies.create')"
            variant="new"
        >
            Nova Deficiência
        </x-buttons.link-button>
    </div>

    <x-table.table :headers="['Deficiência / CID', 'Ativo', 'Ações']">
        @foreach($deficiency as $item)
            <tr>
                
                <x-table.td>
                    <strong>{{ $item->name }}</strong><br>
                    <small class="text-muted">{{ $item->cid_code ?? 'S/ CID' }}</small>
                </x-table.td>

                <x-table.td >
                    @if($item->is_active)
                        <span class="text-success font-weight-bold">SIM</span>
                    @else
                        <span class="text-danger font-weight-bold">NÃO</span>
                    @endif
                </x-table.td>

                <x-table.td>
                    <x-table.actions>

                        <x-buttons.link-button
                            :href="route('specialized-educational-support.deficiencies.show', $item)"
                            variant="info"
                        >
                            ver
                        </x-buttons.link-button>

                        <x-buttons.link-button
                            :href="route('specialized-educational-support.deficiencies.edit', $item)"
                            variant="warning"
                        >
                            Editar
                        </x-buttons.link-button>

                        <form action="{{ route('specialized-educational-support.deficiencies.deactivate', $item) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <x-buttons.submit-button variant="secondary">
                                Ativar/Desativar
                            </x-buttons.submit-button>
                        </form>

                        <form action="{{ route('specialized-educational-support.deficiencies.destroy', $item) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <x-buttons.submit-button
                                variant="danger"
                                onclick="return confirm('Deseja excluir este registro?')"
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