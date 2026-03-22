@extends('layouts.master')

@section('title', "Editar - $location->name")

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Pontos de Referência' => route('inclusive-radar.locations.index'),
            $location->name => route('inclusive-radar.locations.show', $location),
            'Editar' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3 align-items-center">
        <div>
            <h2 class="text-title">Editar Ponto de Referência</h2>
            <p class="text-muted">
                Atualize as informações de:
                <strong class="text-purple-dark">{{ $location->name }}</strong>
            </p>
        </div>
        <div>
            <x-buttons.link-button href="{{ route('inclusive-radar.locations.show', $location) }}" variant="secondary">
                <i class="fas fa-times"></i> Cancelar
            </x-buttons.link-button>
        </div>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('inclusive-radar.locations.update', $location) }}" method="POST">
            @method('PUT')

            <div class="col-lg-5 border-end">
                <x-forms.section title="Vínculo e Identificação" />

                <div class="col-md-12">
                    <x-forms.select
                        name="institution_id"
                        id="institution_select"
                        label="Instituição Base"
                        required
                        :options="$institutions"
                        :selected="old('institution_id', $location->institution_id)"
                    />
                </div>

                <div class="col-md-12">
                    <x-forms.input
                        name="name"
                        id="location_name"
                        label="Nome do Local"
                        required
                        :value="old('name', $location->name)"
                        placeholder="Ex: Bloco Acadêmico II"
                    />
                </div>

                <div class="col-md-12">
                    <x-forms.input
                        name="type"
                        id="location_type"
                        label="Tipo de Local"
                        :value="old('type', $location->type)"
                        placeholder="Ex: Pavilhão, Bloco, Laboratório..."
                    />
                </div>

                <div class="col-md-12">
                    <x-forms.textarea
                        name="description"
                        label="Descrição/Observações"
                        rows="3"
                        :value="old('description', $location->description)"
                        placeholder="Detalhes adicionais sobre o local..."
                    />
                </div>

                <div class="col-md-12 mt-4">
                    <x-forms.checkbox
                        name="is_active"
                        label="Ponto Ativo no Sistema"
                        description="Define se este local aparecerá no mapa público"
                        :checked="old('is_active', $location->is_active)"
                    />
                </div>

                <hr class="mt-4 opacity-0">

                <x-forms.section title="Coordenadas do Mapa" />

                <div class="col-md-12">
                    <label for="lat_manual" class="form-label fw-bold text-purple-dark mb-0">Latitude</label>
                    <small class="text-muted d-block mb-2">Preenchida automaticamente pelo mapa ou manualmente.</small>
                    <input type="number"
                           step="any"
                           id="lat_manual"
                           name="latitude"
                           class="form-control"
                           placeholder="-14.2350"
                           value="{{ old('latitude', $location->latitude) }}"
                    >
                </div>

                <div class="col-md-12 mt-3">
                    <label for="lng_manual" class="form-label fw-bold text-purple-dark mb-0">Longitude</label>
                    <small class="text-muted d-block mb-2">Preenchida automaticamente pelo mapa ou manualmente.</small>
                    <input type="number"
                           step="any"
                           id="lng_manual"
                           name="longitude"
                           class="form-control"
                           placeholder="-51.9253"
                           value="{{ old('longitude', $location->longitude) }}"
                    >
                </div>
            </div>

            <div class="col-lg-7 bg-light">
                <x-forms.section title="Localização no Mapa" id="map-section-title" />

                <div class="sticky-top" style="top:20px; z-index:1;">
                    <section aria-labelledby="map-section-title">
                        <x-forms.maps.location
                            :institution="$selectedInstitution"
                            :location="$location"
                            height="550px"
                            label="Localização no Campus"
                        />
                    </section>
                </div>
            </div>

            <div class="col-12 d-flex justify-content-end gap-3 border-top pt-4 px-4 pb-4 mt-4">
                <x-buttons.link-button href="{{ route('inclusive-radar.locations.show', $location) }}" variant="secondary">
                    <i class="fas fa-times"></i> Cancelar
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit">
                    <i class="fas fa-save mr-2"></i> Salvar
                </x-buttons.submit-button>
            </div>
        </x-forms.form-card>
    </div>

    @push('scripts')
        <script>
            window.institutionsData = @json($institutionsData);

            document.addEventListener('DOMContentLoaded', function() {
                const select = document.getElementById('institution_select');
                if (select) {
                    select.addEventListener('change', function() {
                        const data = window.institutionsData[this.value];
                        if (data && window.locationMapInstance) {
                            window.locationMapInstance.updateLocation(
                                parseFloat(data.latitude),
                                parseFloat(data.longitude),
                                true,
                                false,
                                parseInt(data.default_zoom)
                            );
                        }
                    });
                }
            });
        </script>
        @vite('resources/js/pages/inclusive-radar/locations.js')
    @endpush
@endsection
