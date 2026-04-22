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

    <div class="d-flex flex-column flex-md-row justify-content-between mb-3 align-items-md-center gap-3">
        <div>
            <h2 class="text-title">Detalhes do Ponto de Referência</h2>
            <p class="text-muted">
                Visualize as informações cadastradas e a posição no mapa:
                <strong>{{ $location->name }}</strong>
            </p>
        </div>

        <div class="d-flex gap-2 justify-content-end ms-md-auto">
            @can('location.edit')
                <x-buttons.link-button
                    :href="route('inclusive-radar.locations.edit', $location)"
                    variant="warning"
                >
                    <i class="fas fa-edit"></i> Editar
                </x-buttons.link-button>
            @endcan

            <x-buttons.link-button
                :href="route('inclusive-radar.locations.index')"
                variant="secondary"
            >
                <i class="fas fa-arrow-left"></i> Voltar
            </x-buttons.link-button>
        </div>
    </div>

    <div class="mt-3">
        <x-show.display-card>
            <div class="row g-0">
                <div class="col-lg-5 border-end">

                    <x-forms.section title="Vínculo e Identificação" />

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

                        <x-show.info-textarea label="Descrição/Observações" column="col-12" :value="$location->description ?: '— Não informada —'" :rich="true"/>

                        <x-show.info-item label="Ativo no Sistema" column="col-12" isBox="true">
                            {{ $location->is_active ? 'Sim' : 'Não' }}
                        </x-show.info-item>
                    </div>

                    <x-forms.section title="Coordenadas" />

                    <div class="row g-3 mb-0">
                        <x-show.info-item label="Latitude" column="col-12" isBox="true">
                            {{ $location->latitude ?: '— Não informada —' }}
                        </x-show.info-item>

                        <x-show.info-item label="Longitude" column="col-12" isBox="true">
                            {{ $location->longitude ?: '— Não informada —' }}
                        </x-show.info-item>
                    </div>
                </div>

                <div class="col-lg-7 bg-light">

                    <x-forms.section title="Localização no Mapa" id="map-section-title" />

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

            <div class="col-12 border-top d-flex justify-content-end align-items-center bg-light no-print mt-4 p-4">
                <div class="d-flex flex-wrap gap-3 justify-content-end">
                    @can('location.destroy')
                        <x-buttons.submit-button
                            type="button"
                            variant="danger"
                            data-bs-toggle="modal"
                            data-bs-target="#globalConfirmActionModal"
                            data-confirm-title="Excluir Ponto de Referencia"
                            data-confirm-message="O ponto {{ $location->name }} sera excluido permanentemente."
                            data-confirm-action="{{ route('inclusive-radar.locations.destroy', $location) }}"
                            data-confirm-method="DELETE"
                            data-confirm-submit-text="Confirmar Exclusao"
                            data-confirm-variant="danger"
                        >
                            <i class="fas fa-trash-alt"></i> Excluir
                        </x-buttons.submit-button>
                    @endcan

                    <x-buttons.link-button
                        :href="route('inclusive-radar.locations.index')"
                        variant="secondary"
                    >
                        <i class="fas fa-arrow-left"></i> Voltar
                    </x-buttons.link-button>
                </div>
            </div>
        </x-show.display-card>
    </div>
@endsection
