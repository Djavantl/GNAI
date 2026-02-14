@extends('layouts.master')

@section('title', 'Categorias de Barreiras')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Categorias de Barreiras' => route('inclusive-radar.barrier-categories.index'),
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Categorias de Barreiras</h2>
            <p class="text-muted text-base">Classificação para o mapeamento de acessibilidade e identificação de obstáculos.</p>
        </div>
        <x-buttons.link-button
            :href="route('inclusive-radar.barrier-categories.create')"
            variant="new"
        >
            Nova Categoria
        </x-buttons.link-button>
    </div>

    <x-table.table :headers="['Nome', 'Vínculos', 'Status', 'Ações']">
        @forelse($categories as $category)
            <tr>
                {{-- NOME: Texto direto na TD como em Alunos --}}
                <x-table.td>{{ $category->name }}</x-table.td>

                {{-- VÍNCULOS: Texto direto --}}
                <x-table.td>{{ $category->barriers_count ?? $category->barriers->count() }}</x-table.td>

                {{-- STATUS: Cores Success/Danger para bater com Students --}}
                <x-table.td>
                    @php
                        $color = $category->is_active ? 'success' : 'danger';
                        $label = $category->is_active ? 'Ativo' : 'Inativo';
                    @endphp
                    <span class="text-{{ $color }} fw-bold">
                        {{ $label }}
                    </span>
                </x-table.td>

                {{-- AÇÕES: Apenas Editar e Excluir --}}
                <x-table.td>
                    <x-table.actions>
                        <x-buttons.link-button
                            :href="route('inclusive-radar.barrier-categories.show', $category)"
                            variant="info"
                        >
                            <i class="fas fa-eye"></i> Ver
                        </x-buttons.link-button>

                        <x-buttons.link-button
                            :href="route('inclusive-radar.barrier-categories.edit', $category)"
                            variant="warning"
                        >
                            <i class="fas fa-edit"></i> Editar
                        </x-buttons.link-button>

                        <form action="{{ route('inclusive-radar.barrier-categories.destroy', $category) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <x-buttons.submit-button
                                variant="danger"
                                onclick="return confirm('Tem certeza que deseja excluir?')"
                            >
                                <i class="fas fa-trash-alt"></i> Excluir
                            </x-buttons.submit-button>
                        </form>
                    </x-table.actions>
                </x-table.td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center text-muted py-4">Nenhuma categoria cadastrada.</td>
            </tr>
        @endforelse
    </x-table.table>
@endsection
