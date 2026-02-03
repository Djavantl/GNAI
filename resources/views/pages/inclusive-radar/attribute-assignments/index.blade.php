@extends('layouts.master')

@section('title', 'Vínculos de Atributos')

@section('content')
    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Vínculos de Atributos</h2>
            <p class="text-muted">Gerencie quais campos técnicos cada tipo de recurso deve possuir no formulário.</p>
        </div>
        <x-buttons.link-button
            :href="route('inclusive-radar.type-attribute-assignments.create')"
            variant="new"
        >
            <i class="fas fa-plus-circle me-1"></i> Novo Vínculo em Massa
        </x-buttons.link-button>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
        </div>
    @endif

    <x-table.table :headers="['Tipo de Recurso', 'Atributos Atrelados (Campos)', 'Ações']">
        @php
            $groupedAssignments = $assignments->groupBy('type.name');
        @endphp

        @forelse($groupedAssignments as $typeName => $items)
            @php
                $firstItem = $items->first();
                $type = $firstItem->type;
            @endphp
            <tr>
                <x-table.td>
                    <strong class="text-purple-dark fs-6">{{ $typeName }}</strong>
                </x-table.td>

                <x-table.td>
                    <div class="d-flex flex-wrap gap-1">
                        @foreach($items as $item)
                            <span class="badge border text-dark bg-light fw-normal">
                                <i class="fas fa-tag text-muted me-1" style="font-size: 0.7rem;"></i>
                                {{ $item->attribute->label }}
                            </span>
                        @endforeach
                    </div>
                </x-table.td>

                <x-table.td>
                    <x-table.actions>
                        <x-buttons.link-button
                            :href="route('inclusive-radar.type-attribute-assignments.edit', ['assignment' => $type->id])"
                            variant="warning"
                        >
                            <i class="fas fa-tasks"></i> Gerenciar
                        </x-buttons.link-button>

                        <form action="{{ route('inclusive-radar.type-attribute-assignments.destroy', ['assignment' => $type->id]) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <x-buttons.submit-button
                                variant="danger"
                                onclick="return confirm('Isso removerá TODOS os atributos vinculados ao tipo {{ $typeName }}. Continuar?')"
                            >
                                <i class="fas fa-trash"></i> Limpar
                            </x-buttons.submit-button>
                        </form>
                    </x-table.actions>
                </x-table.td>
            </tr>
        @empty
            <tr>
                <td colspan="3" class="text-center text-muted py-5">
                    <i class="fas fa-layer-group fa-3x mb-3 opacity-20"></i>
                    <p class="mb-0">Nenhum tipo de recurso possui atributos vinculados ainda.</p>
                </td>
            </tr>
        @endforelse
    </x-table.table>
@endsection
