@extends('layouts.master')

@section('title', 'Pontos de Referência')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Pontos de Referência' => route('inclusive-radar.locations.index'),
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Pontos de Referência</h2>
            <p class="text-muted">Gerencie os prédios, salas e locais específicos dentro de cada instituição.</p>
        </div>
        <x-buttons.link-button
            :href="route('inclusive-radar.locations.create')"
            variant="new"
        >
            Novo Ponto
        </x-buttons.link-button>
    </div>

    <x-table.table :headers="['Nome', 'Instituição', 'Tipo', 'Status', 'Ações']">
        @forelse($locations as $loc)
            <tr>
                {{-- NOME --}}
                <x-table.td>{{ $loc->name ?? 'N/A' }}</x-table.td>

                {{-- INSTITUIÇÃO --}}
                <x-table.td>{{ $loc->institution->name ?? 'N/A' }}</x-table.td>

                {{-- TIPO --}}
                <x-table.td>{{ $loc->type ?? 'N/A' }}</x-table.td>

                {{-- STATUS --}}
                <x-table.td>
                    @php
                        $statusColor = $loc->is_active ? 'success' : 'danger';
                        $statusLabel = $loc->is_active ? 'Ativo' : 'Inativo';
                    @endphp
                    <span class="text-{{ $statusColor }} fw-bold">
                        {{ $statusLabel }}
                    </span>
                </x-table.td>

                {{-- AÇÕES --}}
                <x-table.td>
                    <x-table.actions>
                        <x-buttons.link-button
                            :href="route('inclusive-radar.locations.show', $loc)"
                            variant="info"
                        >
                            <i class="fas fa-eye"></i> Ver
                        </x-buttons.link-button>

                        <x-buttons.link-button
                            :href="route('inclusive-radar.locations.edit', $loc)"
                            variant="warning"
                        >
                            <i class="fas fa-edit"></i> Editar
                        </x-buttons.link-button>

                        <form action="{{ route('inclusive-radar.locations.destroy', $loc) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <x-buttons.submit-button
                                variant="danger"
                                onclick="return confirm('Deseja remover este ponto de referência?')"
                            >
                                <i class="fas fa-trash-alt"></i> Excluir
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
