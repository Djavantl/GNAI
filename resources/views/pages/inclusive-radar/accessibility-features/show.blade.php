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

    <div class="d-flex flex-column flex-md-row justify-content-between mb-3 align-items-md-center gap-3">
        <div>
            <h2 class="text-title">Detalhes do Recurso de Acessibilidade</h2>
            <p class="text-muted">
                Visualize as informações cadastrais e status do recurso: <strong>{{ $feature->name }}</strong>
            </p>
        </div>

        <div class="d-flex gap-2 justify-content-end ms-md-auto">
            @can('accessibility-feature.edit')
                <x-buttons.link-button :href="route('inclusive-radar.accessibility-features.edit', $feature)" variant="warning">
                    <i class="fas fa-edit"></i> Editar
                </x-buttons.link-button>
            @endcan

            <x-buttons.link-button :href="route('inclusive-radar.accessibility-features.index')" variant="secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </x-buttons.link-button>
        </div>
    </div>

    <div class="mt-3">
        <div class="custom-table-card bg-white shadow-sm">

            <x-forms.section title="Identificação do Recurso" />

            <div class="row g-3">
                <x-show.info-item label="Nome do Recurso" column="col-md-12" isBox="true">
                    <strong>{{ $feature->name }}</strong>
                </x-show.info-item>

                <x-show.info-textarea label="Descrição Detalhada" column="col-md-12" :value="$feature->description ?: '---'" :rich="true"/>
            </div>

            <x-forms.section title="Configurações de Status" />

            <div class="row g-3">
                <x-show.info-item label="Recurso Ativo" column="col-md-12" isBox="true">
                    {{ $feature->is_active ? 'Sim' : 'Não' }}
                </x-show.info-item>
            </div>

            <div class="col-12 border-top p-4 d-flex justify-content-end align-items-center bg-light no-print mt-4">
                <div class="d-flex flex-wrap gap-3 justify-content-end">
                    @can('accessibility-feature.destroy')
                        <form action="{{ route('inclusive-radar.accessibility-features.destroy', $feature) }}"
                              method="POST"
                              onsubmit="return confirm('Deseja excluir permanentemente?')">
                            @csrf
                            @method('DELETE')

                            <x-buttons.submit-button variant="danger">
                                <i class="fas fa-trash-alt"></i> Excluir
                            </x-buttons.submit-button>
                        </form>
                    @endcan

                    <x-buttons.link-button :href="route('inclusive-radar.accessibility-features.index')" variant="secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </x-buttons.link-button>
                </div>
            </div>
        </div>
    </div>
@endsection
