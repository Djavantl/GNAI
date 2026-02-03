@extends('layouts.master')

@section('title', 'Pontos de Referência')

@section('content')
    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Pontos de Referência (Locations)</h2>
            <p class="text-muted">Gerencie os prédios, salas e locais específicos dentro de cada campus.</p>
        </div>
        <x-buttons.link-button
            :href="route('inclusive-radar.locations.create')"
            variant="new"
        >
            Novo Ponto
        </x-buttons.link-button>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $error)
                <p class="mb-0">{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <x-table.table :headers="['Nome / Prédio', 'Instituição (Campus)', 'Tipo', 'Status', 'Ações']">
        @forelse($locations as $loc)
            <tr>
                <x-table.td>
                    <strong>{{ $loc->name }}</strong><br>
                    <small class="text-muted font-mono" style="font-size: 10px;">{{ $loc->latitude }}, {{ $loc->longitude }}</small>
                </x-table.td>

                <x-table.td>
                    <span class="text-muted italic">{{ $loc->institution->name }}</span>
                </x-table.td>

                <x-table.td class="text-center">
                    <span class="badge bg-light text-secondary border text-uppercase">
                        {{ $loc->type ?? 'Geral' }}
                    </span>
                </x-table.td>

                <x-table.td class="text-center">
                    @if($loc->is_active)
                        <span class="text-success font-weight-bold">ATIVO</span>
                    @else
                        <span class="text-danger font-weight-bold">INATIVO</span>
                    @endif
                </x-table.td>

                <x-table.td>
                    <x-table.actions>
                        {{-- Botão Editar --}}
                        <x-buttons.link-button
                            :href="route('inclusive-radar.locations.edit', $loc)"
                            variant="warning"
                        >
                            Editar
                        </x-buttons.link-button>

                        {{-- Botão Alternar Status (Ativar/Desativar) --}}
                        <form action="{{ route('inclusive-radar.locations.toggle-active', $loc) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <x-buttons.submit-button
                                :variant="$loc->is_active ? 'secondary' : 'success'"
                            >
                                {{ $loc->is_active ? 'Desativar' : 'Ativar' }}
                            </x-buttons.submit-button>
                        </form>

                        {{-- Botão Excluir --}}
                        <form action="{{ route('inclusive-radar.locations.destroy', $loc) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <x-buttons.submit-button
                                variant="danger"
                                onclick="return confirm('Deseja remover este ponto de referência?')"
                            >
                                Excluir
                            </x-buttons.submit-button>
                        </form>
                    </x-table.actions>
                </x-table.td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center text-muted py-4">Nenhum ponto de referência cadastrado.</td>
            </tr>
        @endforelse
    </x-table.table>
@endsection
