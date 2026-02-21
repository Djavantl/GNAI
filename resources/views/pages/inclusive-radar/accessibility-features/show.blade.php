@extends('layouts.master')

@section('title', "$feature->name")

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Recursos de Acessibilidade' => route('inclusive-radar.accessibility-features.index'),
            $feature->name => null
        ]" />
    </div>

    {{-- Cabeçalho --}}
    <div class="d-flex justify-content-between mb-3 align-items-center">
        <div>
            <h2 class="text-title">Detalhes do Recurso de Acessibilidade</h2>
            <p class="text-muted">Visualize as informações cadastrais e status do recurso: <strong>{{ $feature->name }}</strong></p>
        </div>
        <div>
            <x-buttons.link-button :href="route('inclusive-radar.accessibility-features.edit', $feature)" variant="warning">
                <i class="fas fa-edit"></i> Editar
            </x-buttons.link-button>

            <x-buttons.link-button :href="route('inclusive-radar.accessibility-features.index')" variant="secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </x-buttons.link-button>
        </div>
    </div>

    <div class="mt-3">
        <div class="custom-table-card bg-white shadow-sm">

            {{-- SEÇÃO 1: Identificação do Recurso --}}
            <x-forms.section title="Identificação do Recurso" />
            <div class="row g-3">
                <x-show.info-item label="Nome do Recurso" column="col-md-6" isBox="true">
                    <strong>{{ $feature->name }}</strong>
                </x-show.info-item>

                <x-show.info-item label="Descrição Detalhada" column="col-md-12" isBox="true">
                    {{ $feature->description ?? '---' }}
                </x-show.info-item>
            </div>

            {{-- SEÇÃO 2: Configurações de Status --}}
            <x-forms.section title="Configurações de Status" />
            <div class="row g-3">
                <x-show.info-item label="Recurso Ativo" column="col-md-6" isBox="true">
                    {{ $feature->is_active ? 'Sim' : 'Não' }}
                </x-show.info-item>
            </div>

            {{-- Rodapé de Ações --}}
            <div class="col-12 border-top p-4 d-flex justify-content-between align-items-center bg-light no-print">
                <div class="text-muted small">
                    <i class="fas fa-id-card me-1" aria-hidden="true"></i> ID no Sistema: #{{ $feature->id }}
                </div>

                <div class="d-flex gap-3">
                    <form action="{{ route('inclusive-radar.accessibility-features.destroy', $feature) }}" method="POST" onsubmit="return confirm('Deseja excluir permanentemente?')">
                        @csrf @method('DELETE')
                        <x-buttons.submit-button variant="danger">
                            <i class="fas fa-trash-alt"></i> Excluir
                        </x-buttons.submit-button>
                    </form>

                    <x-buttons.link-button :href="route('inclusive-radar.accessibility-features.index')" variant="secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </x-buttons.link-button>
                </div>
            </div>
        </div>
    </div>
@endsection
