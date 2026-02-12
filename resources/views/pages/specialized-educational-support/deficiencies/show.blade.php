@extends('layouts.app')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Deficiências' => route('specialized-educational-support.deficiencies.index'),
            $deficiency->name => null
        ]" />
    </div>


    {{-- Cabeçalho da Página --}}
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <div>
            <h2 class="text-title">Detalhes da Deficiência</h2>
            <p class="text-muted">
                Informações técnicas e descritivas para fins de laudo e apoio pedagógico.
            </p>
        </div>
        <div class="d-flex gap-2">
            <x-buttons.link-button :href="route('specialized-educational-support.deficiencies.edit', $deficiency->id)" variant="warning">
                <i class="fas fa-edit"></i> Editar Cadastro
            </x-buttons.link-button>

            <x-buttons.link-button :href="route('specialized-educational-support.deficiencies.index')" variant="secondary">
                Voltar
            </x-buttons.link-button>
        </div>
    </div>

    <div class="custom-table-card bg-white shadow-sm">
        <div class="row g-0">
            
            {{-- SEÇÃO: IDENTIFICAÇÃO --}}
            <x-forms.section title="Identificação Clínica" />
            
            <x-show.info-item label="Nome da Deficiência / Condição" column="col-md-8" isBox="true">
                <strong class="text-purple-dark">{{ $deficiency->name }}</strong>
            </x-show.info-item>

            <x-show.info-item label="Código CID" column="col-md-4" isBox="true">
                    {{ $deficiency->cid_code ?? 'Não informado' }}
            </x-show.info-item>

            <x-show.info-item label="Status no Sistema" column="col-md-12" isBox="true">
                @if($deficiency->is_active)
                    <span class="text-success fw-bold">
                        <i class="fas fa-toggle-on me-1"></i> ATIVA
                    </span>
                @else
                    <span class="text-muted fw-bold">
                        <i class="fas fa-toggle-off me-1"></i> INATIVA
                    </span>
                @endif
            </x-show.info-item>

            {{-- SEÇÃO: DESCRIÇÃO --}}
            <x-forms.section title="Descrição e Observações Técnicas" />

            <x-show.info-textarea label="Descrição Detalhada" column="col-md-12" isBox="true">
                {{ $deficiency->description ?? 'Nenhuma descrição técnica foi cadastrada para esta deficiência.' }}
            </x-show.info-textarea>

            {{-- RODAPÉ --}}
            <div class="col-12 border-top p-4 d-flex justify-content-between align-items-center bg-light no-print">
                <div class="text-muted small">
                    <i class="fas fa-fingerprint me-1"></i> Registro ID: #{{ $deficiency->id }} | 
                    Cadastrado em: {{ \Carbon\Carbon::parse($deficiency->created_at)->format('d/m/Y') }}
                </div>
                
                <div class="d-flex gap-3">
                    <form action="{{ route('specialized-educational-support.deficiencies.destroy', $deficiency->id) }}" 
                          method="POST" 
                          onsubmit="return confirm('Excluir esta deficiência? Alunos vinculados a este registro podem ser afetados.')">
                        @csrf
                        @method('DELETE')
                        <x-buttons.submit-button variant="danger">
                            Excluir Registro
                        </x-buttons.submit-button>
                    </form>

                    <x-buttons.link-button :href="route('specialized-educational-support.deficiencies.edit', $deficiency->id)" variant="warning">
                        Editar Informações
                    </x-buttons.link-button>
                </div>
            </div>
        </div>
    </div>
@endsection