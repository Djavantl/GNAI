@extends('layouts.master')

@section('title', 'Cadastrar - Instituição')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Instituições' => route('inclusive-radar.institutions.index'),
            'Cadastrar' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Nova Instituição Base</h2>
            <p class="text-muted">Defina o ponto central e as informações da sede para o mapa de barreiras.</p>
        </div>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('inclusive-radar.institutions.store') }}" method="POST">

            {{-- LADO ESQUERDO --}}
            <div class="col-lg-5 border-end">

                <x-forms.section title="1. Informações Gerais" />

                <div class="col-md-12">
                    <x-forms.input name="name" label="Nome da Instituição" required aria-required="true"
                                   :value="old('name')" placeholder="Ex: IFBA - Campus Guanambi" />
                </div>

                <div class="col-md-12">
                    <x-forms.input name="short_name" label="Sigla / Nome Curto"
                                   :value="old('short_name')" placeholder="Ex: IFBA-GBI" />
                </div>

                <div class="col-md-12 mt-3">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <x-forms.input id="city_search" name="city" label="Cidade" required
                                           aria-required="true" :value="old('city')" />
                        </div>
                        <div class="col-md-4">
                            <x-forms.input id="state_search" name="state" label="Estado" required
                                           aria-required="true" :value="old('state')" />
                        </div>
                    </div>
                </div>

                <div class="col-md-12 mt-3">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <x-forms.input id="district_search" name="district" label="Bairro / Distrito"
                                           :value="old('district')" />
                        </div>
                        <div class="col-md-7">
                            <x-forms.input id="address_search" name="address" label="Rua / Logradouro"
                                           :value="old('address')" />
                        </div>
                    </div>
                </div>

                <div class="col-md-12 mt-4">
                    <x-forms.checkbox name="is_active" label="Instituição Ativa"
                                      description="Define se esta sede estará disponível para o mapeamento público"
                                      :checked="old('is_active', true)" />
                </div>

                <hr class="mt-4 opacity-0">

                <x-forms.section title="2. Configurações do Mapa" />

                <div class="col-md-12 mb-4">
                    <label for="zoom_range" class="form-label fw-bold text-purple-dark mb-1">Zoom Padrão</label>
                    <small id="zoom_help" class="text-muted d-block mb-2">Define o nível de aproximação inicial no mapa.</small>

                    <div class="d-flex align-items-center gap-3">
                        <input type="range" name="default_zoom" id="zoom_range" min="1" max="20"
                               value="{{ old('default_zoom', 16) }}"
                               class="form-range custom-range"
                               aria-describedby="zoom_help"
                               oninput="document.getElementById('zoom_val').innerText = this.value">
                        <span id="zoom_val" class="badge bg-purple-dark p-2" style="min-width:40px;">
                    {{ old('default_zoom', 16) }}
                </span>
                    </div>
                </div>

                {{-- LATITUDE --}}
                <div class="col-md-12">
                    <label for="lat_manual" class="form-label fw-bold text-purple-dark mb-0">Latitude Sede</label>
                    <small id="lat_help" class="text-muted d-block mb-2">Preenchida pelo mapa ou manualmente em Graus Decimais.</small>
                    <input type="number" step="any" id="lat_manual" class="form-control"
                           placeholder="-14.2350"
                           aria-describedby="lat_help"
                           value="{{ old('latitude', -14.2350) }}">
                </div>

                {{-- LONGITUDE --}}
                <div class="col-md-12 mt-3">
                    <label for="lng_manual" class="form-label fw-bold text-purple-dark mb-0">Longitude Sede</label>
                    <small id="lng_help" class="text-muted d-block mb-2">Preenchida pelo mapa ou manualmente em Graus Decimais.</small>
                    <input type="number" step="any" id="lng_manual" class="form-control"
                           placeholder="-51.9253"
                           aria-describedby="lng_help"
                           value="{{ old('longitude', -51.9253) }}">
                </div>
            </div>

            {{-- LADO DIREITO — MAPA --}}
            <div class="col-lg-7 bg-light">
                <x-forms.section title="3. Localize a Sede no Mapa" id="map-section-title" />

                <div class="sticky-top" style="top:20px; z-index:1;">
                    <section aria-labelledby="map-section-title">
                        <x-forms.maps.institution
                            :lat="old('latitude', -14.2350)"
                            :lng="old('longitude', -51.9253)"
                            :zoom="old('default_zoom', 16)"
                            height="550px"
                            label="Localize a Sede no Mapa"
                        />
                    </section>
                </div>
            </div>

            {{-- BOTÕES --}}
            <div class="col-12 d-flex justify-content-end gap-3 border-top pt-4 px-4 pb-4 mt-4">
                <x-buttons.link-button href="{{ route('inclusive-radar.institutions.index') }}" variant="secondary">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit">
                    <i class="fas fa-save mr-2"></i> Salvar Instituição
                </x-buttons.submit-button>
            </div>

        </x-forms.form-card>
    </div>
@endsection
