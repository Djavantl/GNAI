@extends('layouts.app')

@section('content')
     <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Cargos' => route('specialized-educational-support.positions.index'),
            $position->name => null
        ]" />
    </div>

    {{-- Cabeçalho da Página --}}
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <div>
            <h2 class="text-title">Detalhes do Cargo</h2>
            <p class="text-muted">
                Gerenciamento de funções e atribuições do suporte especializado.
            </p>
        </div>
        <div class="d-flex gap-2">
            <x-buttons.link-button :href="route('specialized-educational-support.positions.edit', $position->id)" variant="warning">
                <i class="fas fa-edit"></i> Editar Cargo
            </x-buttons.link-button>

            <x-buttons.link-button :href="route('specialized-educational-support.positions.index')" variant="secondary">
                Voltar
            </x-buttons.link-button>
        </div>
    </div>

    <div class="custom-table-card bg-white shadow-sm">
        <div class="row g-0">
            
            {{-- SEÇÃO: IDENTIFICAÇÃO --}}
            <x-forms.section title="Informações do Cargo" />
            
            <x-show.info-item label="Nome do Cargo / Função" column="col-md-8" isBox="true">
                <strong class="text-purple-dark">{{ $position->name }}</strong>
            </x-show.info-item>

            <x-show.info-item label="Status de Disponibilidade" column="col-md-4" isBox="true">
                @if($position->is_active)
                    <span class="text-success fw-bold">
                        <i class="fas fa-check-circle me-1"></i> ATIVO
                    </span>
                @else
                    <span class="text-muted fw-bold">
                        <i class="fas fa-ban me-1"></i> DESATIVADO
                    </span>
                @endif
            </x-show.info-item>

            {{-- SEÇÃO: DESCRIÇÃO --}}
            <x-forms.section title="Atribuições e Descrição" />

            <x-show.info-textarea label="Descrição da Função" column="col-md-12" isBox="true">
                {{ $position->description ?? 'Nenhuma descrição detalhada foi fornecida para este cargo.' }}
            </x-show.info-textarea>

            {{-- RODAPÉ --}}
            <div class="col-12 border-top p-4 d-flex justify-content-between align-items-center bg-light no-print">
                <div class="text-muted small">
                    <i class="fas fa-briefcase me-1"></i> ID do Cargo: #{{ $position->id }} | 
                    Criado em: {{ \Carbon\Carbon::parse($position->created_at)->format('d/m/Y') }}
                </div>
                
                <div class="d-flex gap-3">
                    <form action="{{ route('specialized-educational-support.positions.destroy', $position->id) }}" 
                          method="POST" 
                          onsubmit="return confirm('Excluir este cargo? Isso pode afetar profissionais vinculados.')">
                        @csrf
                        @method('DELETE')
                        <x-buttons.submit-button variant="danger">
                            <i class="fas fa-trash-alt me-1"></i> Remover Cargo
                        </x-buttons.submit-button>
                    </form>

                    <x-buttons.link-button :href="route('specialized-educational-support.positions.edit', $position->id)" variant="warning">
                        <i class="fas fa-edit me-1"></i> Editar Informações
                    </x-buttons.link-button>
                </div>
            </div>
        </div>
    </div>
@endsection