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
            <i class="fas fa-plus-circle me-1"></i> Novo Atributo
        </x-buttons.link-button>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div>
    @endif

    <x-table.table :headers="['Rótulo / Nome Técnico', 'Tipo de Campo', 'Obrigatório', 'Ativo', 'Ações']">
        @forelse($attributes as $attr)
            <tr>
                <x-table.td>
                    <strong class="text-purple-dark d-block">{{ $attr->label }}</strong>
                    <small class="text-muted font-mono italic">{{ $attr->name }}</small>
                </x-table.td>

                <x-table.td class="text-center">
                    <span class="badge bg-light text-muted border font-mono">
                        {{ strtoupper($attr->field_type) }}
                    </span>
                </x-table.td>

                <x-table.td class="text-center">
                    @if($attr->is_required)
                        <span class="badge border shadow-sm"
                              style="background-color: #fff7ed; color: #c2410c; border-color: #ffedd5 !important;">
                            SIM
                        </span>
                    @else
                        <span class="text-muted small">Não</span>
                    @endif
                </x-table.td>

                <x-table.td class="text-center">
                    @if($attr->is_active)
                        <span class="text-success font-weight-bold">SIM</span>
                    @else
                        <span class="text-danger font-weight-bold">NÃO</span>
                    @endif
                </x-table.td>

                <x-table.td>
                    <x-table.actions>
                        {{-- Editar --}}
                        <x-buttons.link-button
                            :href="route('inclusive-radar.type-attributes.edit', $attr)"
                            variant="warning"
                        >
                            Editar
                        </x-buttons.link-button>

                        {{-- Ativar/Desativar (Padrão Cinza/Verde com Texto) --}}
                        <form action="{{ route('inclusive-radar.type-attributes.toggle', $attr) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <x-buttons.submit-button
                                :variant="$attr->is_active ? 'secondary' : 'success'"
                            >
                                {{ $attr->is_active ? 'Desativar' : 'Ativar' }}
                            </x-buttons.submit-button>
                        </form>

                        {{-- Excluir --}}
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
                <td colspan="5" class="text-center text-muted py-4">Nenhum atributo personalizado cadastrado.</td>
            </tr>
        @endforelse
    </x-table.table>

    <style>
        .text-purple-dark { color: #4c1d95; }
    </style>
@endsection
