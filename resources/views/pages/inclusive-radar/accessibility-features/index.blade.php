@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Recursos de Acessibilidade</h2>
            <p class="text-muted">Gestão de serviços, adaptações e recursos promotores de acessibilidade.</p>
        </div>
        <x-buttons.link-button
            :href="route('inclusive-radar.accessibility-features.create')"
            variant="new"
        >
            Novo Recurso
        </x-buttons.link-button>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <x-table.table :headers="['Nome', 'Descrição', 'Status', 'Ativo', 'Ações']">
        @forelse($features as $feature)
            <tr>
                <x-table.td>
                    <strong>{{ $feature->name }}</strong>
                </x-table.td>

                <x-table.td>
                    <span class="text-muted small">
                        {{ $feature->description ?: 'Sem descrição informada.' }}
                    </span>
                </x-table.td>

                <x-table.td class="text-center">
                    <span class="text-muted font-weight-bold text-uppercase" style="font-size: 0.8rem;">
                        {{ $feature->is_active ? 'Disponível' : 'Indisponível' }}
                    </span>
                </x-table.td>

                <x-table.td class="text-center">
                    @if($feature->is_active)
                        <span class="text-success font-weight-bold">SIM</span>
                    @else
                        <span class="text-danger font-weight-bold">NÃO</span>
                    @endif
                </x-table.td>

                <x-table.td>
                    <x-table.actions>
                        <x-buttons.link-button
                            :href="route('inclusive-radar.accessibility-features.edit', $feature)"
                            variant="warning"
                        >
                            Editar
                        </x-buttons.link-button>

                        <form action="{{ route('inclusive-radar.accessibility-features.toggle', $feature) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <x-buttons.submit-button
                                :variant="$feature->is_active ? 'secondary' : 'success'"
                            >
                                {{ $feature->is_active ? 'Desativar' : 'Ativar' }}
                            </x-buttons.submit-button>
                        </form>

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
                <td colspan="5" class="text-center text-muted py-4">Nenhum recurso de acessibilidade cadastrado.</td>
            </tr>
        @endforelse
    </x-table.table>
@endsection
