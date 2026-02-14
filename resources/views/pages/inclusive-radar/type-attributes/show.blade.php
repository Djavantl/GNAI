@extends('layouts.master')

@section('title', "$typeAttribute->label")

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Atributos de Recursos' => route('inclusive-radar.type-attributes.index'),
            $typeAttribute->label => route('inclusive-radar.type-attributes.show', $typeAttribute),
        ]" />
    </div>

    {{-- Cabeçalho --}}
    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Detalhes do Atributo</h2>
            <p class="text-muted">Visualize as informações cadastrais e configurações do atributo: <strong>{{ $typeAttribute->label }}</strong></p>
        </div>
        <div class="text-end">
            <span class="d-block text-muted small uppercase fw-bold">ID do Registro</span>
            <span class="badge bg-purple fs-6">#{{ $typeAttribute->id }}</span>
        </div>
    </div>

    <div class="mt-3">
        <div class="custom-table-card bg-white shadow-sm">

            {{-- SEÇÃO 1: Identificação do Atributo --}}
            <x-forms.section title="Identificação do Atributo" />
            <div class="row g-3 mb-4">
                <x-show.info-item label="Rótulo de Exibição (Label)" column="col-md-6" isBox="true">
                    <strong>{{ $typeAttribute->label }}</strong>
                </x-show.info-item>

                <x-show.info-item label="Nome Técnico (Identificador)" column="col-md-6" isBox="true">
                    {{ $typeAttribute->name }}
                </x-show.info-item>

                <x-show.info-item label="Tipo de Dado" column="col-md-6" isBox="true">
                    @php
                        $types = [
                            'string' => 'Texto Curto (String)',
                            'text' => 'Texto Longo (TextArea)',
                            'integer' => 'Número Inteiro',
                            'decimal' => 'Número Decimal',
                            'boolean' => 'Sim/Não (Booleano)',
                            'date' => 'Data'
                        ];
                    @endphp
                    {{ $types[$typeAttribute->field_type] ?? '---' }}
                </x-show.info-item>
            </div>

            {{-- SEÇÃO 2: Configurações e Visibilidade --}}
            <x-forms.section title="Configurações e Visibilidade" />
            <div class="row g-3 mb-4">
                <x-show.info-item label="Campo Obrigatório" column="col-md-6" isBox="true">
                    {{ $typeAttribute->is_required ? 'Sim' : 'Não' }}
                </x-show.info-item>

                <x-show.info-item label="Ativo no Sistema" column="col-md-6" isBox="true">
                    {{ $typeAttribute->is_active ? 'Sim' : 'Não' }}
                </x-show.info-item>
            </div>

            {{-- Rodapé de Ações --}}
            <div class="col-12 border-top p-4 d-flex justify-content-between align-items-center bg-light no-print">
                <div class="text-muted small">
                    <i class="fas fa-id-card me-1"></i> ID do Sistema: #{{ $typeAttribute->id }}
                </div>

                <div class="d-flex gap-3">
                    <form action="{{ route('inclusive-radar.type-attributes.destroy', $typeAttribute) }}"
                          method="POST"
                          onsubmit="return confirm('ATENÇÃO: Esta ação excluirá este atributo. Confirmar?')">
                        @csrf
                        @method('DELETE')
                        <x-buttons.submit-button variant="danger">
                            <i class="fas fa-trash-alt"></i> Excluir Atributo
                        </x-buttons.submit-button>
                    </form>

                    <x-buttons.link-button :href="route('inclusive-radar.type-attributes.edit', $typeAttribute)" variant="warning">
                        <i class="fas fa-edit"></i> Editar Atributo
                    </x-buttons.link-button>

                    <x-buttons.link-button :href="route('inclusive-radar.type-attributes.index')" variant="secondary">
                        <i class="fas fa-arrow-left"></i> Voltar para Lista
                    </x-buttons.link-button>
                </div>
            </div>
        </div>
    </div>
@endsection
