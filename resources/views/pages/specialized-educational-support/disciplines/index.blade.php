@extends('layouts.master')

@section('title', 'Disciplinas')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Disciplinas' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <h2 class="text-title">Disciplinas</h2>
        <x-buttons.link-button :href="route('specialized-educational-support.disciplines.create')" variant="new">
             <i class="fas fa-plus me-1"></i> Adicionar Disciplina
        </x-buttons.link-button>
    </div>

    <x-table.table :headers="['Nome', 'Status', 'Ações']">
    @foreach($disciplines as $discipline)
        <tr>
            <x-table.td>
                <span class="fw-bold text-purple-dark">{{ $discipline->name }}</span>
            </x-table.td>
            <x-table.td>
                <span class="text-{{ $discipline->is_active ? 'success' : 'danger' }} fw-bold">
                    <i class="fas fa-{{ $discipline->is_active ? 'check' : 'times' }}-circle me-1"></i>
                    {{ $discipline->is_active ? 'Ativo' : 'Inativo' }}
                </span>
            </x-table.td>
            <x-table.td>
                <x-table.actions>
                    <x-buttons.link-button :href="route('specialized-educational-support.disciplines.show', $discipline)" variant="info">
                        Ver
                    </x-buttons.link-button>

                    <x-buttons.link-button :href="route('specialized-educational-support.disciplines.edit', $discipline)" variant="warning">
                        Editar
                    </x-buttons.link-button>

                    <form action="{{ route('specialized-educational-support.disciplines.destroy', $discipline) }}" method="POST">
                        @csrf @method('DELETE')
                        <x-buttons.submit-button variant="danger" onclick="return confirm('Deseja excluir esta disciplina?')">
                            Excluir
                        </x-buttons.submit-button>
                    </form>
                </x-table.actions>
            </x-table.td>
        </tr>
    @endforeach
    </x-table.table>
@endsection