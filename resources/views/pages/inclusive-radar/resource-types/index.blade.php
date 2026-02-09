@extends('layouts.master')

@section('title', 'Tipos de Recursos')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Tipos de Recursos' => route('inclusive-radar.resource-types.index'),
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Tipos de Recursos</h2>
            <p class="text-muted">Definição de categorias e naturezas para Tecnologias e Materiais.</p>
        </div>
        <x-buttons.link-button
            :href="route('inclusive-radar.resource-types.create')"
            variant="new"
        >
            Novo Tipo
        </x-buttons.link-button>
    </div>

    <x-table.table :headers="['Nome do Tipo', 'Natureza', 'Finalidade', 'Status', 'Ações']">
        @forelse($resourceTypes as $type)
            <tr>
                {{-- NOME: Texto direto --}}
                <x-table.td>{{ $type->name ?? 'N/A' }}</x-table.td>

                {{-- NATUREZA: Texto simples --}}
                <x-table.td>
                    {{ $type->is_digital ? 'Digital' : 'Físico' }}
                </x-table.td>

                {{-- FINALIDADE: Texto direto separado por barra --}}
                <x-table.td>
                    @php
                        $apps = [];
                        if($type->for_assistive_technology) $apps[] = 'Tecnologia Assistiva';
                        if($type->for_educational_material) $apps[] = 'Materiais Pedagógicos Acessíveis';
                    @endphp
                    {{ count($apps) > 0 ? implode(' / ', $apps) : 'N/A' }}
                </x-table.td>

                {{-- STATUS: Padronizado com Students --}}
                <x-table.td>
                    @php
                        $statusColor = $type->is_active ? 'success' : 'danger';
                        $statusLabel = $type->is_active ? 'Ativo' : 'Inativo';
                    @endphp
                    <span class="text-{{ $statusColor }} fw-bold">
                        {{ $statusLabel }}
                    </span>
                </x-table.td>

                {{-- AÇÕES: Apenas Editar e Excluir --}}
                <x-table.td>
                    <x-table.actions>
                        <x-buttons.link-button
                            :href="route('inclusive-radar.resource-types.show', $type)"
                            variant="info"
                        >
                            Ver
                        </x-buttons.link-button>

                        <x-buttons.link-button
                            :href="route('inclusive-radar.resource-types.edit', $type)"
                            variant="warning"
                        >
                            Editar
                        </x-buttons.link-button>

                        <form action="{{ route('inclusive-radar.resource-types.destroy', $type) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <x-buttons.submit-button
                                variant="danger"
                                onclick="return confirm('Tem certeza que deseja remover este tipo?')"
                            >
                                Excluir
                            </x-buttons.submit-button>
                        </form>
                    </x-table.actions>
                </x-table.td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center text-muted py-4">Nenhum tipo de recurso cadastrado.</td>
            </tr>
        @endforelse
    </x-table.table>
@endsection
