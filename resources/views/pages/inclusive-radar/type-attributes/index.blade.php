@extends('layouts.master')

@section('title', 'Atributos de Recursos')

@section('content')

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Atributos Personalizados</h2>
            <p class="text-muted">Gerencie campos dinâmicos para detalhamento técnico dos recursos.</p>
        </div>
        <x-buttons.link-button
            :href="route('inclusive-radar.type-attributes.create')"
            variant="new"
        >
            Novo Atributo
        </x-buttons.link-button>
    </div>

    <x-table.table :headers="['Rótulo / Nome', 'Obrigatório', 'Status', 'Ações']">
        @forelse($attributes as $attr)
            <tr>
                {{-- RÓTULO / NOME --}}
                <x-table.td>
                    {{ $attr->label ?? 'N/A' }}
                    <small class="text-muted d-block">{{ $attr->name }}</small>
                </x-table.td>

                {{-- OBRIGATÓRIO --}}
                <x-table.td>
                    @if($attr->is_required)
                        <span class="text-warning fw-bold">Sim</span>
                    @else
                        <span class="text-muted">Não</span>
                    @endif
                </x-table.td>

                {{-- STATUS: Padrão Students --}}
                <x-table.td>
                    @php
                        $statusColor = $attr->is_active ? 'success' : 'danger';
                        $statusLabel = $attr->is_active ? 'Ativo' : 'Inativo';
                    @endphp
                    <span class="text-{{ $statusColor }} fw-bold">
                        {{ $statusLabel }}
                    </span>
                </x-table.td>

                {{-- AÇÕES --}}
                <x-table.td>
                    <x-table.actions>
                        <x-buttons.link-button
                            :href="route('inclusive-radar.type-attributes.show', $attr)"
                            variant="info"
                        >
                            Ver
                        </x-buttons.link-button>

                        <x-buttons.link-button
                            :href="route('inclusive-radar.type-attributes.edit', $attr)"
                            variant="warning"
                        >
                            Editar
                        </x-buttons.link-button>

                        <form action="{{ route('inclusive-radar.type-attributes.destroy', $attr) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <x-buttons.submit-button
                                variant="danger"
                                onclick="return confirm('Tem certeza que deseja remover este atributo?')"
                            >
                                Excluir
                            </x-buttons.submit-button>
                        </form>
                    </x-table.actions>
                </x-table.td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center text-muted py-4">Nenhum atributo personalizado cadastrado.</td>
            </tr>
        @endforelse
    </x-table.table>
@endsection
