@extends('layouts.master')

@section('title', "$institution->name")

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Instituições' => route('inclusive-radar.institutions.index'),
            $institution->name => null
        ]" />
    </div>

    {{-- Cabeçalho --}}
    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Detalhes da Instituição</h2>
            <p class="text-muted">
                Visualize as informações cadastrais e a localização no mapa da instituição:
                <strong>{{ $institution->name }}</strong>
            </p>
        </div>

        <div class="text-end">
            <span class="d-block text-muted small uppercase fw-bold">ID do Registro</span>
            <span class="badge bg-purple fs-6">#{{ $institution->id }}</span>
        </div>
    </div>

    <div class="mt-3">
        <x-show.display-card>
            {{-- ROW PRINCIPAL --}}
            <div class="row g-0">

                {{-- LADO ESQUERDO --}}
                <div class="col-lg-5 border-end">
                    <x-forms.section title="1. Informações Gerais" />

                    <div class="row g-3 mb-0">
                        <x-show.info-item label="Nome da Instituição" column="col-12" isBox="true">
                            {{ $institution->name }}
                        </x-show.info-item>

                        <x-show.info-item label="Sigla / Nome Curto" column="col-12" isBox="true">
                            {{ $institution->short_name ?: '— Não informada —' }}
                        </x-show.info-item>

                        <x-show.info-item label="Cidade" column="col-md-8" isBox="true">
                            {{ $institution->city }}
                        </x-show.info-item>

                        <x-show.info-item label="Estado" column="col-md-4" isBox="true">
                            {{ $institution->state }}
                        </x-show.info-item>

                        <x-show.info-item label="Bairro / Distrito" column="col-md-5" isBox="true">
                            {{ $institution->district ?: '— Não informado —' }}
                        </x-show.info-item>

                        <x-show.info-item label="Rua / Logradouro" column="col-md-7" isBox="true">
                            {{ $institution->address ?: '— Não informado —' }}
                        </x-show.info-item>

                        <x-show.info-item label="Instituição Ativa" column="col-12" isBox="true">
                            {{ $institution->is_active ? 'Sim' : 'Não' }}
                        </x-show.info-item>
                    </div>

                    <x-forms.section title="2. Configurações do Mapa" />

                    <div class="row g-3 mb-0">
                        <x-show.info-item label="Zoom Padrão" column="col-12" isBox="true">
                            {{ $institution->default_zoom }}
                        </x-show.info-item>

                        <x-show.info-item label="Latitude Sede" column="col-12" isBox="true">
                            {{ $institution->latitude ?: '— Não informada —' }}
                        </x-show.info-item>

                        <x-show.info-item label="Longitude Sede" column="col-12" isBox="true">
                            {{ $institution->longitude ?: '— Não informada —' }}
                        </x-show.info-item>
                    </div>
                </div>

                {{-- LADO DIREITO — MAPA --}}
                <div class="col-lg-7 bg-light">
                    <x-forms.section title="3. Localização no Mapa" id="map-section-title" />

                    <div class="sticky-top" style="top:20px; z-index:1;">
                        <section aria-labelledby="map-section-title">
                            <x-show.maps.institution
                                :institution="$institution"
                                :lat="$institution->latitude"
                                :lng="$institution->longitude"
                                :zoom="$institution->default_zoom"
                                height="550px"
                                label="Localização da Instituição"
                            />
                        </section>
                    </div>
                </div>

            </div> {{-- fim row principal --}}

            {{-- RODAPÉ DE AÇÕES --}}
            <div class="col-12 border-top d-flex justify-content-between align-items-center bg-light no-print mt-4 p-4">
                <div class="text-muted small">
                    <i class="fas fa-id-card me-1"></i> ID do Sistema: #{{ $institution->id }}
                </div>

                <div class="d-flex gap-3">
                    <x-buttons.link-button :href="route('inclusive-radar.institutions.edit', $institution)" variant="warning">
                        Editar Instituição
                    </x-buttons.link-button>

                    <x-buttons.link-button :href="route('inclusive-radar.institutions.index')" variant="secondary">
                        Voltar para Lista
                    </x-buttons.link-button>
                </div>
            </div>
        </x-show.display-card>
    </div>
@endsection
