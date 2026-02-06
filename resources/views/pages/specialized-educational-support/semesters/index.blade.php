@extends('layouts.master')

@section('title', 'Gestão de Semestres')

@section('content')
    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Semestres Letivos</h2>
            <p class="text-muted">Configuração de períodos para organização dos atendimentos e relatórios.</p>
        </div>
        <x-buttons.link-button
            :href="route('specialized-educational-support.semesters.create')"
            variant="new"
        >
            Novo Semestre
        </x-buttons.link-button>
    </div>

    <x-table.table :headers="['Ano / Período', 'Rótulo Identificador', 'Status', 'Ações']">
        @forelse($semesters as $semester)
            <tr class="{{ $semester->is_current ? 'table-primary' : '' }}">
                <x-table.td>
                    <div class="fw-bold">{{ $semester->year }}</div>
                    <small class="text-muted text-uppercase">{{ $semester->term }}º Período</small>
                </x-table.td>

                <x-table.td>
                    <span class="text-uppercase fw-bold">{{ $semester->label }}</span>
                </x-table.td>

                <x-table.td >
                    @if($semester->is_current)
                        <span class="text-success">
                            SEMESTRE ATUAL
                        </span>
                    @else
                        <span class="text-muted">Histórico</span>
                    @endif
                </x-table.td>

                <x-table.td>
                    <x-table.actions>

                        <x-buttons.link-button
                            :href="route('specialized-educational-support.semesters.show', $semester)"
                            variant="info-outline"
                        >
                            ver
                        </x-buttons.link-button>

                        <x-buttons.link-button
                            :href="route('specialized-educational-support.semesters.edit', $semester)"
                            variant="warning"
                        >
                            Editar
                        </x-buttons.link-button>

                        @if(!$semester->is_current)
                            <form action="{{ route('specialized-educational-support.semesters.setCurrent', $semester) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <x-buttons.submit-button variant="success">
                                    Definir Atual
                                </x-buttons.submit-button>
                            </form>
                        @endif

                        <form action="{{ route('specialized-educational-support.semesters.destroy', $semester) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <x-buttons.submit-button
                                variant="danger"
                                onclick="return confirm('Tem certeza que deseja excluir este semestre?')"
                            >
                                Excluir
                            </x-buttons.submit-button>
                        </form>
                    </x-table.actions>
                </x-table.td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center text-muted py-5">
                    Nenhum semestre cadastrado.
                </td>
            </tr>
        @endforelse
    </x-table.table>

    <div class="mt-4">
        <div class="alert alert-light border small text-muted">
            <i class="fas fa-info-circle mr-1"></i>
            O <strong>Semestre Atual</strong> determina qual período será selecionado por padrão em novos lançamentos e filtros de relatórios.
        </div>
    </div>
@endsection