@extends('layouts.master')

@section('title', "$assignment->name")

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Vínculos de Atributos' => route('inclusive-radar.type-attribute-assignments.index'),
            $assignment->name => null
        ]" />
    </div>

    {{-- Cabeçalho --}}
    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Detalhes do Tipo e Atributos</h2>
            <p class="text-muted">Visualize os campos técnicos vinculados a este tipo de recurso.</p>
        </div>
        <div class="text-end">
            <span class="d-block text-muted small uppercase fw-bold">ID do Tipo</span>
            <span class="badge bg-purple fs-6">#{{ $assignment->id }}</span>
        </div>
    </div>

    <div class="mt-3">
        <div class="custom-table-card bg-white shadow-sm">

            {{-- SEÇÃO 1: Informações do Tipo --}}
            <x-forms.section title="Informações do Tipo" />
            <div class="row g-3 mb-3">
                <x-show.info-item label="Nome do Tipo" column="col-md-6" isBox="true">
                    {{ $assignment->name }}
                </x-show.info-item>

                <x-show.info-item label="Utilizada em:" column="col-md-6" isBox="true">
                    {{ $assignment->for_assistive_technology ? 'Tecnologia Assistiva' : 'Materiais Pedagógicos Acessíveis' }}
                </x-show.info-item>
            </div>

            <div class="row g-3 mb-3">
                <x-show.info-item label="Digital" column="col-md-6" isBox="true">
                    {{ $assignment->is_digital ? 'Sim' : 'Não' }}
                </x-show.info-item>

                <x-show.info-item label="Ativo" column="col-md-6" isBox="true">
                    {{ $assignment->is_active ? 'Sim' : 'Não' }}
                </x-show.info-item>
            </div>

            {{-- SEÇÃO 2: Atributos Vinculados --}}
            <x-forms.section title="Campos Técnicos Vinculados" />
            <div class="row g-3 mb-4">
                <x-show.info-item label="Atributos Vinculados" column="col-md-12" isBox="true">
                    {{ $assignment->attributes->pluck('label')->join(', ') ?: '---' }}
                </x-show.info-item>
            </div>

            {{-- Rodapé de Ações --}}
            <div class="col-12 border-top p-4 d-flex justify-content-between align-items-center bg-light no-print">
                <div class="text-muted small">
                    <i class="fas fa-id-card me-1"></i> ID do Sistema: #{{ $assignment->id }}
                </div>

                <div class="d-flex gap-3">
                    <form action="{{ route('inclusive-radar.type-attribute-assignments.destroy', $assignment) }}"
                          method="POST"
                          onsubmit="return confirm('ATENÇÃO: Esta ação removerá todos os atributos vinculados. Confirmar?')">
                        @csrf
                        @method('DELETE')
                        <x-buttons.submit-button variant="danger">
                            Excluir Todos os Vínculos
                        </x-buttons.submit-button>
                    </form>

                    <x-buttons.link-button :href="route('inclusive-radar.type-attribute-assignments.edit', $assignment)" variant="warning">
                        Editar Vínculos
                    </x-buttons.link-button>

                    <x-buttons.link-button :href="route('inclusive-radar.type-attribute-assignments.index')" variant="secondary">
                        Voltar para Lista
                    </x-buttons.link-button>
                </div>
            </div>
        </div>
    </div>
@endsection
