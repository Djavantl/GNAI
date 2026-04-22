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

    <div class="d-flex flex-column flex-md-row justify-content-between mb-3 align-items-md-center gap-3">
        <div>
            <h2 class="text-title">Detalhes da Instituição</h2>
            <p class="text-muted">
                Visualize as informações cadastrais e a localização no mapa da instituição:
                <strong>{{ $institution->name }}</strong>
            </p>
        </div>

        <div class="d-flex gap-2 justify-content-end ms-md-auto">
            @can('institution.edit')
                <x-buttons.link-button :href="route('inclusive-radar.institutions.edit', $institution)" variant="warning">
                    <i class="fas fa-edit"></i> Editar
                </x-buttons.link-button>
            @endcan

            <x-buttons.link-button :href="route('inclusive-radar.institutions.index')" variant="secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </x-buttons.link-button>
        </div>
    </div>

    <div class="mt-3">
        <x-show.display-card>
            <div class="row g-0">
                <div class="col-lg-5 border-end">

                    <x-forms.section title="Informações Gerais" />

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

                    <x-forms.section title="Configurações do Mapa" />

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

                <div class="col-lg-7 bg-light">

                    <x-forms.section title="Localização no Mapa" id="map-section-title" />

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
            </div>

            <div class="col-12 border-top d-flex justify-content-end align-items-center bg-light no-print mt-4 p-4">
                <div class="d-flex flex-wrap gap-3 justify-content-end">
                    @can('institution.destroy')
                        <x-buttons.submit-button
                            type="button"
                            variant="danger"
                            data-bs-toggle="modal"
                            data-bs-target="#globalConfirmActionModal"
                            data-confirm-title="Excluir Instituicao"
                            data-confirm-message="A instituicao {{ $institution->name }} e seus dados relacionados serao excluidos permanentemente."
                            data-confirm-action="{{ route('inclusive-radar.institutions.destroy', $institution) }}"
                            data-confirm-method="DELETE"
                            data-confirm-submit-text="Confirmar Exclusao"
                            data-confirm-variant="danger"
                        >
                            <i class="fas fa-trash-alt"></i> Excluir
                        </x-buttons.submit-button>
                    @endcan

                    <x-buttons.link-button :href="route('inclusive-radar.institutions.index')" variant="secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </x-buttons.link-button>
                </div>
            </div>
        </x-show.display-card>
    </div>
@endsection
