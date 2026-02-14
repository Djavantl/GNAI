@extends('layouts.master')

@section('title', 'Vínculos de Atributos')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Vínculos de Atributos' => route('inclusive-radar.type-attribute-assignments.index'),
        ]" />
    </div>

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

    <x-table.table :headers="['Tipo de Recurso', 'Ações']">
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

                {{-- AÇÕES: Simples e direto --}}
                <x-table.td>
                    <x-table.actions>
                        {{-- AÇÕES: Padronizado com ícones --}}
                        <x-buttons.link-button
                            :href="route('inclusive-radar.type-attribute-assignments.show', ['assignment' => $type->id])"
                            variant="info"
                        >
                            <i class="fas fa-eye me-1"></i> Ver
                        </x-buttons.link-button>

                        <x-buttons.link-button
                            :href="route('inclusive-radar.type-attribute-assignments.edit', ['assignment' => $type->id])"
                            variant="warning"
                        >
                            <i class="fas fa-edit"></i> Editar
                        </x-buttons.link-button>

                        <form action="{{ route('inclusive-radar.type-attribute-assignments.destroy', ['assignment' => $type->id]) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <x-buttons.submit-button
                                variant="danger"
                                onclick="return confirm('Isso removerá TODOS os atributos vinculados ao tipo {{ $typeName }}. Continuar?')"
                            >
                                <i class="fas fa-trash-alt me-1"></i> Limpar Vínculos
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
