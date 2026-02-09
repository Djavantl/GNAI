@extends('layouts.master')

@section('title', "$location->name")

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Pontos de Referência' => route('inclusive-radar.locations.index'),
            $location->name => null
        ]" />
    </div>

    {{-- Cabeçalho --}}
    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Detalhes do Ponto de Referência</h2>
            <p class="text-muted">
                Visualize as informações cadastradas e a posição no mapa:
                <strong>{{ $location->name }}</strong>
            </p>
        </div>

        <div class="text-end">
            <span class="d-block text-muted small uppercase fw-bold">ID do Registro</span>
            <span class="badge bg-purple fs-6">#{{ $location->id }}</span>
        </div>
    </div>

    <div class="mt-3">
        <x-show.display-card>

            <div class="row g-0">

                {{-- LADO ESQUERDO --}}
                <div class="col-lg-5 border-end">

                    <x-forms.section title="1. Vínculo e Identificação" />

                    <div class="row g-3 mb-0">

                        <x-show.info-item label="Instituição Base" column="col-12" isBox="true">
                            {{ $location->institution->name }}
                        </x-show.info-item>

                        <x-show.info-item label="Nome do Local" column="col-12" isBox="true">
                            {{ $location->name }}
                        </x-show.info-item>

                        <x-show.info-item label="Tipo de Local" column="col-12" isBox="true">
                            {{ $location->type ?: '— Não informado —' }}
                        </x-show.info-item>

                        <x-show.info-item label="Descrição/Observações" column="col-12" isBox="true">
                            {{ $location->description ?: '— Não informada —' }}
                        </x-show.info-item>

                        <x-show.info-item label="Ativo no Sistema" column="col-12" isBox="true">
                            {{ $location->is_active ? 'Sim' : 'Não' }}
                        </x-show.info-item>

                    </div>

                    <x-forms.section title="2. Coordenadas" />

                    <div class="row g-3 mb-0">

                        <x-show.info-item label="Latitude" column="col-12" isBox="true">
                            {{ $location->latitude ?: '— Não informada —' }}
                        </x-show.info-item>

                        <x-show.info-item label="Longitude" column="col-12" isBox="true">
                            {{ $location->longitude ?: '— Não informada —' }}
                        </x-show.info-item>

                    </div>
                </div>


                {{-- LADO DIREITO — MAPA --}}
                <div class="col-lg-7 bg-light">
                    <x-forms.section title="3. Localização no Mapa" id="map-section-title" />

                    <div class="sticky-top" style="top:20px; z-index:1;">
                        <section aria-labelledby="map-section-title">

                            <x-show.maps.location
                                :location="$location"
                                :institution="$location->institution"
                                height="550px"
                                label="Localização do Ponto"
                            />

                        </section>
                    </div>
                </div>

            </div>


            {{-- RODAPÉ --}}
            <div class="col-12 border-top d-flex justify-content-between align-items-center bg-light no-print mt-4 p-4">

                <div class="text-muted small">
                    <i class="fas fa-id-card me-1"></i>
                    ID do Sistema: #{{ $location->id }}
                </div>

                <div class="d-flex gap-3">

                    <x-buttons.link-button
                        :href="route('inclusive-radar.locations.edit', $location)"
                        variant="warning"
                    >
                        Editar Local
                    </x-buttons.link-button>

                    <x-buttons.link-button
                        :href="route('inclusive-radar.locations.index')"
                        variant="secondary"
                    >
                        Voltar para Lista
                    </x-buttons.link-button>

                </div>
            </div>

        </x-show.display-card>
    </div>
@endsection
