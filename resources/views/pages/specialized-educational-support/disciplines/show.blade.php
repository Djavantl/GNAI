@extends('layouts.app')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Disciplinas' => route('specialized-educational-support.disciplines.index'),
            $discipline->name => null
        ]" />
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <div>
            <h2 class="text-title">Detalhes da Disciplina</h2>
            <p class="text-muted">Informações cadastrais e descrição da matéria.</p>
        </div>
        <div class="d-flex gap-2">
            <x-buttons.link-button :href="route('specialized-educational-support.disciplines.edit', $discipline)" variant="warning">
                <i class="fas fa-edit"></i> Editar Disciplina
            </x-buttons.link-button>
            <x-buttons.link-button :href="route('specialized-educational-support.disciplines.index')" variant="secondary">
                Voltar
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

            <x-forms.section title="Vínculos" />

            <div class="col-12 p-4">
                <p class="text-muted mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Esta disciplina pode estar vinculada a múltiplos cursos e planos de AEE.
                </p>
            </div>

            {{-- Rodapé de Ações --}}
            <div class="col-12 border-top p-4 d-flex justify-content-between align-items-center bg-light no-print">
                <div class="text-muted small">
                    <i class="fas fa-fingerprint me-1"></i> ID da Disciplina: #{{ $discipline->id }}
                </div>
                
                <div class="d-flex gap-3">
                    <form action="{{ route('specialized-educational-support.disciplines.destroy', $discipline) }}" 
                          method="POST" 
                          onsubmit="return confirm('Deseja realmente excluir esta disciplina?')">
                        @csrf
                        @method('DELETE')
                        <x-buttons.submit-button variant="danger">
                            <i class="fas fa-trash-alt me-1"></i> Excluir
                        </x-buttons.submit-button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection