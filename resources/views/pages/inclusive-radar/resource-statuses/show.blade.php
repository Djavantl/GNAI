@extends('layouts.master')

@section('title', "$resourceStatus->name")

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Status dos Recursos' => route('inclusive-radar.resource-statuses.index'),
            $resourceStatus->name => route('inclusive-radar.resource-statuses.show', $resourceStatus),
        ]" />
    </div>

    {{-- Cabeçalho --}}
    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Detalhes do Status do Recurso</h2>
            <p class="text-muted">
                Visualize as regras e configurações do status:
                <strong>{{ $resourceStatus->name }}</strong>
            </p>
        </div>

        <div class="text-end">
            <span class="d-block text-muted small uppercase fw-bold">ID do Registro</span>
            <span class="badge bg-purple fs-6">#{{ $resourceStatus->id }}</span>
        </div>
    </div>

    <div class="mt-3">
        <div class="custom-table-card bg-white shadow-sm">

            {{-- SEÇÃO 1 — Identificação --}}
            <x-forms.section title="Identificação do Status" />
            <div class="row g-3 mb-4">

                <x-show.info-item label="Nome exibido" column="col-md-12" isBox="true">
                    <strong>{{ $resourceStatus->name }}</strong>
                </x-show.info-item>

                <x-show.info-item label="Descrição / Finalidade" column="col-md-12" isBox="true">
                    {{ $resourceStatus->description ?: '—' }}
                </x-show.info-item>

            </div>


            {{-- SEÇÃO 2 — Regras de Bloqueio --}}
            <x-forms.section title="Regras de Bloqueio" />
            <div class="row g-3 mb-4">

                <x-show.info-item label="Bloqueia Empréstimo" column="col-md-6" isBox="true">
                    {{ $resourceStatus->blocks_loan ? 'Sim' : 'Não' }}
                </x-show.info-item>

                <x-show.info-item label="Bloqueia Acesso" column="col-md-6" isBox="true">
                    {{ $resourceStatus->blocks_access ? 'Sim' : 'Não' }}
                </x-show.info-item>

            </div>


            {{-- SEÇÃO 3 — Aplicabilidade e Ativação --}}
            <x-forms.section title="Aplicabilidade e Ativação" />
            <div class="row g-3 mb-4">

                <x-show.info-item label="Ativo no Sistema" column="col-md-12" isBox="true">
                    {{ $resourceStatus->is_active ? 'Sim' : 'Não' }}
                </x-show.info-item>

            </div>

            <div class="row g-3 mb-4">
                <x-show.info-item label="Tecnologias Assistivas" column="col-md-6" isBox="true">
                    {{ $resourceStatus->for_assistive_technology ? 'Sim' : 'Não' }}
                </x-show.info-item>

                <x-show.info-item label="Materiais Pedagógicos Acessíveis" column="col-md-6" isBox="true">
                    {{ $resourceStatus->for_educational_material ? 'Sim' : 'Não' }}
                </x-show.info-item>
            </div>


            {{-- Rodapé de ações --}}
            <div class="col-12 border-top p-4 d-flex justify-content-between align-items-center bg-light no-print">

                <div class="text-muted small">
                    <i class="fas fa-id-card me-1"></i>
                    ID do Sistema: #{{ $resourceStatus->id }}
                </div>

                <div class="d-flex gap-3">

                    <x-buttons.link-button
                        :href="route('inclusive-radar.resource-statuses.edit', $resourceStatus)"
                        variant="warning">
                        <i class="fas fa-edit"></i> Editar Status
                    </x-buttons.link-button>

                    <x-buttons.link-button
                        :href="route('inclusive-radar.resource-statuses.index')"
                        variant="secondary">
                        <i class="fas fa-arrow-left"></i> Voltar para Lista
                    </x-buttons.link-button>
                </div>
            </div>
        </div>
    </div>
@endsection
