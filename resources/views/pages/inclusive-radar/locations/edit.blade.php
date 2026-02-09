@extends('layouts.master')

@section('title', "Editar - $location->name ")

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Pontos de Referência' => route('inclusive-radar.locations.index'),
            $location->name => route('inclusive-radar.locations.show', $location),
            'Editar' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Editar Ponto de Referência</h2>
            <p class="text-muted">
                Atualize as informações de:
                <strong class="text-purple-dark">{{ $location->name }}</strong>
            </p>
        </div>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('inclusive-radar.locations.update', $location) }}" method="POST">
            @method('PUT')

            {{-- LADO ESQUERDO --}}
            <div class="col-lg-5 border-end">

                <x-forms.section title="1. Vínculo e Identificação" />

                {{-- INSTITUIÇÃO BASE --}}
                <div class="col-md-12 mb-3">
                    <label for="institution_select" class="form-label fw-bold text-purple-dark italic">
                        Instituição Base
                        <span aria-hidden="true">*</span>
                        <span class="visually-hidden">(campo obrigatório)</span>
                    </label>

                    <select name="institution_id" id="institution_select" required aria-required="true"
                            class="form-select border-purple-light shadow-sm">
                        <option value="">Selecione a Instituição...</option>

                        @foreach($institutions as $inst)
                            <option value="{{ $inst->id }}"
                                    data-lat="{{ $inst->latitude }}"
                                    data-lng="{{ $inst->longitude }}"
                                    data-zoom="{{ $inst->default_zoom ?? 16 }}"
                                {{ old('institution_id', $location->institution_id) == $inst->id ? 'selected' : '' }}>
                                {{ $inst->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- NOME DO LOCAL --}}
                <div class="col-md-12">
                    <x-forms.input name="name" id="location_name" label="Nome do Local" required
                                   aria-required="true" :value="old('name', $location->name)" placeholder="Ex: Bloco Acadêmico II" />
                </div>

                {{-- TIPO DE LOCAL --}}
                <div class="col-md-12">
                    <x-forms.input name="type" id="location_type" label="Tipo de Local"
                                   :value="old('type', $location->type)" placeholder="Ex: Pavilhão, Bloco, Laboratório..." />
                </div>

                {{-- DESCRIÇÃO --}}
                <div class="col-md-12 mt-3">
                    <label for="description" class="form-label fw-bold text-purple-dark">Descrição/Observações</label>
                    <textarea name="description" id="description" rows="3" class="form-control border-purple-light"
                              placeholder="Detalhes adicionais sobre o local...">{{ old('description', $location->description) }}</textarea>
                </div>

                {{-- STATUS ATIVO --}}
                <div class="col-md-12 mt-4">
                    <x-forms.checkbox name="is_active" id="is_active" label="Ponto Ativo no Sistema"
                                      description="Define se este local aparecerá no mapa público"
                                      :checked="old('is_active', $location->is_active)" />
                </div>

                <hr class="mt-4 opacity-0">

                <x-forms.section title="2. Coordenadas do Mapa" />

                {{-- LATITUDE --}}
                <div class="col-md-12">
                    <label for="lat_manual" class="form-label fw-bold text-purple-dark mb-0">Latitude</label>
                    <small id="lat_help" class="text-muted d-block mb-2">Preenchida automaticamente pelo mapa ou manualmente.</small>
                    <input type="number" step="any" id="lat_manual" class="form-control"
                           placeholder="-14.2350"
                           aria-describedby="lat_help"
                           value="{{ old('latitude', $location->latitude) }}">
                </div>

                {{-- LONGITUDE --}}
                <div class="col-md-12 mt-3">
                    <label for="lng_manual" class="form-label fw-bold text-purple-dark mb-0">Longitude</label>
                    <small id="lng_help" class="text-muted d-block mb-2">Preenchida automaticamente pelo mapa ou manualmente.</small>
                    <input type="number" step="any" id="lng_manual" class="form-control"
                           placeholder="-51.9253"
                           aria-describedby="lng_help"
                           value="{{ old('longitude', $location->longitude) }}">
                </div>
            </div>

            {{-- LADO DIREITO — MAPA --}}
            <div class="col-lg-7 bg-light">
                <x-forms.section title="3. Localização no Mapa" id="map-section-title" />

                <div class="sticky-top" style="top:20px; z-index:1;">
                    <section aria-labelledby="map-section-title">
                        @php
                            // Identifica a instituição atual para centralizar o mapa caso não haja coordenadas no local
                            $selectedInstitutionId = old('institution_id', $location->institution_id);
                            $selectedInstitution = $institutions->firstWhere('id', $selectedInstitutionId);
                        @endphp

                        <x-forms.maps.location
                            :institution="$selectedInstitution"
                            :location="$location"
                            height="550px"
                            label="Localização no Campus"
                        />
                    </section>
                </div>
            </div>

            {{-- BOTÕES --}}
            <div class="col-12 d-flex justify-content-end gap-3 border-top pt-4 px-4 pb-4 mt-4">
                <x-buttons.link-button href="{{ route('inclusive-radar.locations.index') }}" variant="secondary">
                    Cancelar
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit px-5">
                    <i class="fas fa-save mr-2"></i> Atualizar Localização
                </x-buttons.submit-button>
            </div>
        </x-forms.form-card>
    </div>

    <script>
        window.institutionsData = @json($institutions);

        document.addEventListener('DOMContentLoaded', function() {
            const institutionSelect = document.getElementById('institution_select');
            if (institutionSelect) {
                institutionSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    const lat = selectedOption.getAttribute('data-lat');
                    const lng = selectedOption.getAttribute('data-lng');
                    const zoom = selectedOption.getAttribute('data-zoom');

                    if (lat && lng && window.locationMapInstance) {
                        window.locationMapInstance.updateLocation(parseFloat(lat), parseFloat(lng), true, false, parseInt(zoom));
                    }
                });
            }
        });
    </script>
@endsection
