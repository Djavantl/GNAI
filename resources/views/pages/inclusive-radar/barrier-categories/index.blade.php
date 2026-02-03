@extends('layouts.master')

@section('title', 'Categorias de Barreiras')

@section('content')
    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Categorias de Barreiras</h2>
            <p class="text-muted">Classificação para o mapeamento de acessibilidade e identificação de obstáculos.</p>
        </div>
        <x-buttons.link-button
            :href="route('inclusive-radar.barrier-categories.create')"
            variant="new"
        >
            Nova Categoria
        </x-buttons.link-button>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm">
            @foreach($errors->all() as $error)
                <p class="mb-0">{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <x-table.table :headers="['Nome da Categoria', 'Descrição', 'Vínculos', 'Ativo', 'Ações']">
        @forelse($categories as $category)
            <tr>
                <x-table.td>
                    <strong class="text-purple-dark">{{ $category->name }}</strong>
                </x-table.td>

                <x-table.td>
                    <span class="text-muted italic" style="font-size: 0.9rem;">
                        {{ Str::limit($category->description, 80) ?: 'Sem descrição informada' }}
                    </span>
                </x-table.td>

                <x-table.td class="text-center">
                    <span class="badge bg-soft-blue text bg-purple px-3">
                        {{ $category->barriers_count ?? $category->barriers->count() }}
                    </span>
                </x-table.td>

                <x-table.td class="text-center">
                    @if($category->is_active)
                        <span class="text-success font-weight-bold">SIM</span>
                    @else
                        <span class="text-danger font-weight-bold">NÃO</span>
                    @endif
                </x-table.td>

                <x-table.td>
                    <x-table.actions>
                        <x-buttons.link-button
                            :href="route('inclusive-radar.barrier-categories.edit', $category)"
                            variant="warning"
                        >
                            Editar
                        </x-buttons.link-button>

                        <form action="{{ route('inclusive-radar.barrier-categories.toggle-active', $category) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <x-buttons.submit-button
                                :variant="$category->is_active ? 'secondary' : 'success'"
                            >
                                {{ $category->is_active ? 'Desativar' : 'Ativar' }}
                            </x-buttons.submit-button>
                        </form>

                        <form action="{{ route('inclusive-radar.barrier-categories.destroy', $category) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <x-buttons.submit-button
                                variant="danger"
                                onclick="return confirm('Tem certeza que deseja excluir? Esta ação não pode ser desfeita se houver barreiras vinculadas.')"
                            >
                                Excluir
                            </x-buttons.submit-button>
                        </form>
                    </x-table.actions>
                </x-table.td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center text-muted py-4">Nenhuma categoria cadastrada.</td>
            </tr>
        @endforelse
    </x-table.table>
@endsection
