@extends('layouts.master')

@section('title', 'Tipos de Recursos')

@section('content')
    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Tipos de Recursos</h2>
            <p class="text-muted">Definição de categorias e naturezas para Tecnologias e Materiais.</p>
        </div>
        <x-buttons.link-button
            :href="route('inclusive-radar.resource-types.create')"
            variant="new"
        >
            <i class="fas fa-plus-circle me-1"></i> Novo Tipo
        </x-buttons.link-button>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div>
    @endif

    <x-table.table :headers="['Nome do Tipo', 'Natureza', 'Finalidade / Aplicação', 'Status', 'Ativo', 'Ações']">
        @forelse($resourceTypes as $type)
            <tr>
                <x-table.td>
                    <strong class="text-purple-dark fs-6">{{ $type->name }}</strong>
                </x-table.td>

                <x-table.td class="text-center">
                    @if($type->is_digital)
                        <span class="badge shadow-sm border"
                              style="background-color: #eef2ff; color: #4338ca; border-color: #e0e7ff !important;">
                            <i class="fas fa-cloud me-1"></i> DIGITAL
                        </span>
                    @else
                        <span class="badge shadow-sm border"
                              style="background-color: #fffbeb; color: #b45309; border-color: #fef3c7 !important;">
                            <i class="fas fa-box me-1"></i> FÍSICO
                        </span>
                    @endif
                </x-table.td>

                <x-table.td class="text-center">
                    <div class="d-flex flex-column gap-1 align-items-center">
                        @if($type->for_assistive_technology)
                            <span class="badge bg-light text-purple-dark border w-100" style="font-size: 0.65rem;">
                                TEC. ASSISTIVA
                            </span>
                        @endif
                        @if($type->for_educational_material)
                            <span class="badge bg-light text-primary border w-100" style="font-size: 0.65rem;">
                                MATERIAL DIDÁTICO
                            </span>
                        @endif
                    </div>
                </x-table.td>

                <x-table.td class="text-center">
                    <span class="text-muted font-weight-bold text-uppercase" style="font-size: 0.75rem;">
                        {{ $type->is_digital ? 'Uso Ilimitado' : 'Controle Patrimonial' }}
                    </span>
                </x-table.td>

                <x-table.td class="text-center">
                    @if($type->is_active)
                        <span class="text-success font-weight-bold">SIM</span>
                    @else
                        <span class="text-danger font-weight-bold">NÃO</span>
                    @endif
                </x-table.td>

                <x-table.td>
                    <x-table.actions>
                        <x-buttons.link-button
                            :href="route('inclusive-radar.resource-types.edit', $type)"
                            variant="warning"
                        >
                            Editar
                        </x-buttons.link-button>

                        <form action="{{ route('inclusive-radar.resource-types.toggle', $type) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <x-buttons.submit-button
                                :variant="$type->is_active ? 'secondary' : 'success'"
                            >
                                {{ $type->is_active ? 'Desativar' : 'Ativar' }}
                            </x-buttons.submit-button>
                        </form>

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
                <td colspan="6" class="text-center text-muted py-4">Nenhum tipo de recurso cadastrado.</td>
            </tr>
        @endforelse
    </x-table.table>

    <style>
        .text-purple-dark { color: #4c1d95; }
    </style>
@endsection
