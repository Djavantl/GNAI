@extends('layouts.master')

@section('title', "$resourceType->name")

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Tipos de Recursos' => route('inclusive-radar.resource-types.index'),
            $resourceType->name => route('inclusive-radar.resource-types.show', $resourceType),
        ]" />
    </div>

    {{-- Cabeçalho --}}
    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Detalhes do Tipo de Recurso</h2>
            <p class="text-muted">Visualize as informações cadastrais e configurações do recurso: <strong>{{ $resourceType->name }}</strong></p>
        </div>
        <div class="text-end">
            <span class="d-block text-muted small uppercase fw-bold">ID do Registro</span>
            <span class="badge bg-purple fs-6">#{{ $resourceType->id }}</span>
        </div>
    </div>

    <div class="mt-3">
        <div class="custom-table-card bg-white shadow-sm">

            {{-- SEÇÃO 1: Identificação da Categoria --}}
            <x-forms.section title="Identificação da Categoria" />
            <div class="row g-3 mb-4">
                <x-show.info-item label="Nome do Tipo" column="col-md-12" isBox="true">
                    <strong>{{ $resourceType->name }}</strong>
                </x-show.info-item>
            </div>

            {{-- SEÇÃO 2: Natureza e Visibilidade --}}
            <x-forms.section title="Natureza e Visibilidade" />
            <div class="row g-3 mb-4">
                <x-show.info-item label="Recurso Digital" column="col-md-6" isBox="true">
                    {{ $resourceType->is_digital ? 'Sim' : 'Não' }}
                </x-show.info-item>

                <x-show.info-item label="Ativo no Sistema" column="col-md-6" isBox="true">
                    {{ $resourceType->is_active ? 'Sim' : 'Não' }}
                </x-show.info-item>
            </div>

            <div class="row g-3 mb-4">
                <x-show.info-item label="Tecnologias Assistivas" column="col-md-6" isBox="true">
                    {{ $resourceType->for_assistive_technology ? 'Sim' : 'Não' }}
                </x-show.info-item>

                <x-show.info-item label="Materiais Pedagógicos Acessíveis" column="col-md-6" isBox="true">
                    {{ $resourceType->for_educational_material ? 'Sim' : 'Não' }}
                </x-show.info-item>
            </div>

            {{-- Rodapé de Ações --}}
            <div class="col-12 border-top p-4 d-flex justify-content-between align-items-center bg-light no-print">
                <div class="text-muted small">
                    <i class="fas fa-id-card me-1"></i> ID do Sistema: #{{ $resourceType->id }}
                </div>

                <div class="d-flex gap-3">
                    <form action="{{ route('inclusive-radar.resource-types.destroy', $resourceType) }}"
                          method="POST"
                          onsubmit="return confirm('ATENÇÃO: Esta ação excluirá este tipo de recurso. Confirmar?')">
                        @csrf
                        @method('DELETE')
                        <x-buttons.submit-button variant="danger">
                            <i class="fas fa-trash-alt"></i> Excluir Tipo de Recurso
                        </x-buttons.submit-button>
                    </form>

                    <x-buttons.link-button :href="route('inclusive-radar.resource-types.edit', $resourceType)" variant="warning">
                        <i class="fas fa-edit"></i> Editar Tipo de Recurso
                    </x-buttons.link-button>

                    <x-buttons.link-button :href="route('inclusive-radar.resource-types.index')" variant="secondary">
                        <i class="fas fa-arrow-left"></i> Voltar para Lista
                    </x-buttons.link-button>
                </div>
            </div>

        </div>
    </div>
@endsection
