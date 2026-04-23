@extends('layouts.app')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Disciplinas' => route('specialized-educational-support.disciplines.index'),
            $discipline->name => null
        ]" />
    </div>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 no-print">
        <div>
            <h2 class="text-title">Detalhes da Disciplina</h2>
            <p class="text-muted">Informações cadastrais e descrição da matéria.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap justify-content-end ms-md-auto">
            @can('discipline.update')
            <x-buttons.link-button :href="route('specialized-educational-support.disciplines.edit', $discipline)" variant="warning">
                <i class="fas fa-edit"></i> Editar
            </x-buttons.link-button>
            @endcan
            <x-buttons.link-button :href="route('specialized-educational-support.disciplines.index')" variant="secondary">
               <i class="fas fa-arrow-left"></i>  Voltar
            </x-buttons.link-button>
        </div>
    </div>

    <div class="custom-table-card bg-white shadow-sm">
        <div class="row g-0">
            <x-forms.section title="Informações Gerais" />

            <x-show.info-item label="Nome da Disciplina" column="col-md-8" isBox="true">
                <strong>{{ $discipline->name }}</strong>
            </x-show.info-item>

            <x-show.info-item label="Status" column="col-md-4" isBox="true">
                <span class="text-{{ $discipline->is_active ? 'success' : 'danger' }} fw-bold">
                    {{ $discipline->is_active ? 'ATIVO' : 'INATIVO' }}
                </span>
            </x-show.info-item>

            <x-show.info-item label="Descrição / Objetivos" column="col-md-12" isBox="true">
                {{ $discipline->description ?? 'Nenhuma descrição informada.' }}
            </x-show.info-item>

            <x-show.info-item 
                label="Cursos vinculados" 
                column="col-md-12" 
                isBox="true"
            >
                @if($discipline->courses->isEmpty())
                    <span class="text-muted">
                        Nenhum curso vinculado.
                    </span>
                @else
                    <ul class="mb-0">
                        @foreach($discipline->courses as $course)
                            <li>
                                {{ $course->name }}
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-show.info-item>

            {{-- Rodapé de Ações --}}
            <div class="col-12 border-top p-4 d-flex flex-wrap justify-content-end gap-2 bg-light no-print">
                <div class="d-flex flex-wrap gap-2">
                    @can('discipline.delete')
                    <x-buttons.submit-button
                        type="button"
                        variant="danger"
                        data-bs-toggle="modal"
                        data-bs-target="#globalConfirmActionModal"
                        data-confirm-title="Excluir Disciplina"
                        data-confirm-message="Deseja realmente excluir esta disciplina?"
                        data-confirm-action="{{ route('specialized-educational-support.disciplines.destroy', $discipline) }}"
                        data-confirm-method="DELETE"
                        data-confirm-submit-text="Confirmar Exclusao"
                        data-confirm-variant="danger"
                    >
                            <i class="fas fa-trash-alt me-1"></i> Excluir
                        </x-buttons.submit-button>
                    @endcan
                </div>
            </div>
        </div>
    </div>
@endsection
