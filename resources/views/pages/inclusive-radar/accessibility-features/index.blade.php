@extends('layouts.master')

@section('title', 'Recursos de Acessibilidade')

@section('content')
    <x-messages.toast />

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Recursos de Acessibilidade</h2>
            <p class="text-muted text-base">Gestão de serviços, adaptações e recursos promotores de acessibilidade.</p>
        </div>
        <x-buttons.link-button
            :href="route('inclusive-radar.accessibility-features.create')"
            variant="new"
        >
            Novo Recurso
        </x-buttons.link-button>
    </div>

    <x-table.table :headers="['Nome', 'Status', 'Ações']">
        @forelse($features as $feature)
            <tr>
                {{-- NOME: Texto direto na TD como em Alunos --}}
                <x-table.td>{{ $feature->name }}</x-table.td>

                {{-- STATUS: Única exceção para cor e negrito, padrão Students --}}
                <x-table.td>
                    @php
                        $color = $feature->is_active ? 'success' : 'danger';
                        $label = $feature->is_active ? 'Ativo' : 'Inativo';
                    @endphp
                    <span class="text-{{ $color }} fw-bold">
                        {{ $label }}
                    </span>
                </x-table.td>

                {{-- AÇÕES: Simples e direto --}}
                <x-table.td>
                    <x-table.actions>
                        <x-buttons.link-button
                            :href="route('inclusive-radar.accessibility-features.show', $feature)"
                            variant="info"
                        >
                            Ver
                        </x-buttons.link-button>

                        <x-buttons.link-button
                            :href="route('inclusive-radar.accessibility-features.edit', $feature)"
                            variant="warning"
                        >
                            Editar
                        </x-buttons.link-button>

                        <form action="{{ route('inclusive-radar.accessibility-features.destroy', $feature) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <x-buttons.submit-button
                                variant="danger"
                                onclick="return confirm('Deseja realmente remover este recurso?')"
                            >
                                Excluir
                            </x-buttons.submit-button>
                        </form>
                    </x-table.actions>
                </x-table.td>
            </tr>
        @empty
            <tr>
                <td colspan="3" class="text-center text-muted py-4">Nenhum recurso de acessibilidade cadastrado.</td>
            </tr>
        @endforelse
    </x-table.table>
@endsection
