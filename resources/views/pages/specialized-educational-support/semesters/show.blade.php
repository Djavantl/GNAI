@extends('layouts.app')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Semestres' => route('specialized-educational-support.semesters.index'),
            $semester->label => null
        ]" />
    </div>

    {{-- Cabeçalho da Página --}}
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <div>
            <h2 class="text-title">Detalhes do Semestre</h2>
            <p class="text-muted">
                Configuração do período letivo e vigência dos atendimentos.
            </p>
        </div>
        <div class="d-flex gap-2">
            <x-buttons.link-button :href="route('specialized-educational-support.semesters.edit', $semester->id)" variant="warning">
                <i class="fas fa-edit"></i> Editar 
            </x-buttons.link-button>

            <x-buttons.link-button :href="route('specialized-educational-support.semesters.index')" variant="secondary">
                <i class="fas fa-arrow-left"></i>Voltar
            </x-buttons.link-button>
        </div>
    </div>

    <div class="custom-table-card bg-white shadow-sm">
        <div class="row g-0">
            
            {{-- SEÇÃO: IDENTIFICAÇÃO DO PERÍODO --}}
            <x-forms.section title="Identificação do Período" />
            
            <x-show.info-item label="Rótulo / Identificador" column="col-md-4" isBox="true">
                <strong class="text-purple-dark">{{ $semester->label }}</strong>
            </x-show.info-item>

            <x-show.info-item label="Ano Letivo" column="col-md-2" isBox="true">
                {{ $semester->year }}
            </x-show.info-item>

            <x-show.info-item label="Etapa (Termo)" column="col-md-2" isBox="true">
                {{ $semester->term }}º Semestre
            </x-show.info-item>

            <x-show.info-item label="Vigência Atual" column="col-md-4" isBox="true">
                @if($semester->is_current)
                    <span class="text-success fw-bold">
                        SEMESTRE ATIVO NO SISTEMA
                    </span>
                @else
                    <span class="text-muted">
                        PERÍODO ENCERRADO / HISTÓRICO
                    </span>
                @endif
            </x-show.info-item>

            {{-- SEÇÃO: DATAS --}}
            <x-forms.section title="Duração do Semestre" />

            <x-show.info-item label="Data de Início" column="col-md-4" isBox="true">
                {{ $semester->start_date ? $semester->start_date->format('d/m/Y') : 'Não definida' }}
            </x-show.info-item>

            <x-show.info-item label="Data de Término" column="col-md-4" isBox="true">
                {{ $semester->end_date ? $semester->end_date->format('d/m/Y') : 'Não definida' }}
            </x-show.info-item>

            <x-show.info-item label="Duração Total" column="col-md-4" isBox="true">
                @if($semester->start_date && $semester->end_date)
                    {{ $semester->start_date->diffInDays($semester->end_date) }} dias letivos aproximados
                @else
                    ---
                @endif
            </x-show.info-item>

            {{-- RODAPÉ --}}
            <div class="col-12 border-top p-4 d-flex justify-content-between align-items-center bg-light no-print">
                <div class="text-muted small">
                    <i class="fas fa-clock me-1"></i> Criado em: {{ $semester->created_at->format('d/m/Y H:i') }}
                </div>
                
                <div class="d-flex gap-3">
                    <form action="{{ route('specialized-educational-support.semesters.destroy', $semester->id) }}" 
                          method="POST" 
                          onsubmit="return confirm('Excluir este semestre? Isso pode afetar os registros de frequência e sessões vinculados a este período.')">
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