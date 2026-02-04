@extends('layouts.master')

@section('title', 'Mapa de Barreiras')

@section('content')
    <x-messages.toast />

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Mapa de Barreiras</h2>
            <p class="text-muted text-base">Contribuições da comunidade para uma instituição mais acessível.</p>
        </div>
        <x-buttons.link-button
            :href="route('inclusive-radar.barriers.create')"
            variant="new"
        >
            Relatar Barreira
        </x-buttons.link-button>
    </div>

    <x-table.table :headers="['Nome', 'Categoria', 'Relator', 'Prioridade', 'Status', 'Ações']">
        @forelse($barriers as $barrier)
            <tr>
                {{-- NOME: Texto direto como em Alunos --}}
                <x-table.td>{{ $barrier->name }}</x-table.td>

                {{-- CATEGORIA --}}
                <x-table.td>{{ $barrier->category->name }}</x-table.td>

                {{-- RELATOR: Direto, usando apenas o small se houver cargo --}}
                <x-table.td>
                    {{ $barrier->is_anonymous ? 'Anônimo' : ($barrier->registeredBy->name ?? 'Sistema') }}
                    @if($barrier->affected_person_role)
                        <small class="text-muted d-block">{{ $barrier->affected_person_role }}</small>
                    @endif
                </x-table.td>

                {{-- PRIORIDADE: Única exceção para cor, seguindo o padrão de Status de Alunos --}}
                <x-table.td>
                    <span class="text-{{ $barrier->priority->color() }} fw-bold">
                        {{ $barrier->priority->label() }}
                    </span>
                </x-table.td>

                {{-- STATUS --}}
                <x-table.td>
                    @php $status = $barrier->latestStatus(); @endphp
                    @if($status)
                        <span class="text-{{ $status->color() }} fw-bold">
                            {{ $status->label() }}
                        </span>
                    @else
                        <span class="text-secondary fw-bold">Pendente</span>
                    @endif
                </x-table.td>

                {{-- AÇÕES --}}
                <x-table.td>
                    <x-table.actions>
                        <x-buttons.link-button
                            :href="route('inclusive-radar.barriers.edit', $barrier)"
                            variant="warning"
                        >
                            Editar
                        </x-buttons.link-button>

                        <form action="{{ route('inclusive-radar.barriers.destroy', $barrier) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <x-buttons.submit-button
                                variant="danger"
                                onclick="return confirm('Deseja remover este relato?')"
                            >
                                Excluir
                            </x-buttons.submit-button>
                        </form>
                    </x-table.actions>
                </x-table.td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center text-muted py-4">Nenhuma barreira identificada até o momento.</td>
            </tr>
        @endforelse
    </x-table.table>

    @if(method_exists($barriers, 'hasPages') && $barriers->hasPages())
        <div class="mt-4">
            {{ $barriers->links() }}
        </div>
    @endif
@endsection
