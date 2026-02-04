@extends('layouts.master')

@section('title', 'Vínculos de Atributos')

@section('content')
    <x-messages.toast />

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Vínculos de Atributos</h2>
            <p class="text-muted">Gerencie quais campos técnicos cada tipo de recurso deve possuir no formulário.</p>
        </div>
        <x-buttons.link-button
            :href="route('inclusive-radar.type-attribute-assignments.create')"
            variant="new"
        >
            Novo Vínculo em Massa
        </x-buttons.link-button>
    </div>

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
                {{-- TIPO DE RECURSO: Texto direto na TD como em Alunos --}}
                <x-table.td>{{ $typeName }}</x-table.td>

                {{-- ATRIBUTOS: Texto direto separado por vírgulas --}}
                <x-table.td>
                    @foreach($items as $item)
                        {{ $item->attribute->label }}{{ !$loop->last ? ',' : '' }}
                    @endforeach
                </x-table.td>

                {{-- AÇÕES: Simples e direto --}}
                <x-table.td>
                    <x-table.actions>
                        <x-buttons.link-button
                            :href="route('inclusive-radar.type-attribute-assignments.edit', ['assignment' => $type->id])"
                            variant="warning"
                        >
                            Editar
                        </x-buttons.link-button>

                        <form action="{{ route('inclusive-radar.type-attribute-assignments.destroy', ['assignment' => $type->id]) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <x-buttons.submit-button
                                variant="danger"
                                onclick="return confirm('Isso removerá TODOS os atributos vinculados ao tipo {{ $typeName }}. Continuar?')"
                            >
                                Excluir
                            </x-buttons.submit-button>
                        </form>
                    </x-table.actions>
                </x-table.td>
            </tr>
        @empty
            <tr>
                <td colspan="3" class="text-center text-muted py-4">
                    Nenhum tipo de recurso possui atributos vinculados ainda.
                </td>
            </tr>
        @endforelse
    </x-table.table>
@endsection
