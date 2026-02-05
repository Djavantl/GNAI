@props([
    'lat' => -14.2350,
    'lng' => -51.9253,
    'zoom' => 16,
    'height' => '500px',
    'latId' => 'lat',
    'lngId' => 'lng',
    'zoomId' => 'zoom_range',
    'label' => 'Localização no Mapa',
    'showLegend' => true,
    'locationId' => null
])

<div {{ $attributes->merge(['class' => 'leaflet-map-container']) }}
     id="leaflet-container-main"
     data-lat="{{ old('latitude', $lat) }}"
     data-lng="{{ old('longitude', $lng) }}"
     data-zoom="{{ old('default_zoom', $zoom) }}"
     data-lat-id="{{ $latId }}"
     data-lng-id="{{ $lngId }}"
     data-zoom-id="{{ $zoomId }}"
     data-location-id="{{ $locationId }}">

    {{-- ===== TÍTULO ACESSÍVEL DO MAPA ===== --}}
    <div class="d-flex justify-content-between align-items-center mb-1">

        {{-- id usado pelo aria-labelledby --}}
        <span
            id="map-label-{{ $latId }}"
            class="form-label fw-bold text-purple-dark mb-0"
        >
            {{ $label }}
        </span>

        <small
            id="map-help-{{ $latId }}"
            class="text-muted italic"
            style="font-size: 0.75rem;"
        >
            Clique no mapa para definir o ponto
        </small>
    </div>

    {{-- ===== MAPA ===== --}}
    <div class="map-wrapper shadow-sm">
        <div
            id="map"
            style="height: {{ $height }};"
            role="application"
            aria-labelledby="map-label-{{ $latId }}"
            aria-describedby="map-help-{{ $latId }}">
        </div>
    </div>

    {{-- ===== DISPLAY COORDENADAS ===== --}}
    <div class="d-flex gap-3 mt-2">
        <div class="small text-muted">
            <span class="fw-bold text-purple-dark">LAT:</span>
            <span id="display-{{ $latId }}">{{ old('latitude', $lat) }}</span>
        </div>

        <div class="small text-muted">
            <span class="fw-bold text-purple-dark">LNG:</span>
            <span id="display-{{ $lngId }}">{{ old('longitude', $lng) }}</span>
        </div>
    </div>

    {{-- INPUT OCULTO (mantido, mas corrigido) --}}
    <input
        type="text"
        name="map_dummy"
        id="map_dummy_{{ $latId }}"
        autocomplete="off"
        hidden
        aria-hidden="true"
    >

    {{-- ===== LEGENDA ===== --}}
    @if($showLegend)
        <div id="map-legend-main" class="map-legend-container d-none mb-3">

            <div id="legend-item-blue" class="map-legend-item d-none">
                <div class="map-legend-dot dot-novo"></div>
                <span class="map-legend-text">
                    <strong class="text-primary">Novo:</strong>
                    Ponto que você está definindo agora (Arraste-o!)
                </span>
            </div>

            <div id="legend-item-red" class="map-legend-item d-none">
                <div class="map-legend-dot dot-sede"></div>
                <span class="map-legend-text">
                    <strong class="text-danger">Sede:</strong>
                    Localização principal da Instituição.
                </span>
            </div>

            <div id="legend-item-grey" class="map-legend-item d-none">
                <div class="map-legend-dot dot-existente"></div>
                <span class="map-legend-text">
                    <strong class="text-secondary">Cadastrados:</strong>
                    Outros pontos já existentes neste instituto.
                </span>
            </div>

        </div>
    @endif
</div>

@push('styles')
    <link rel="stylesheet"
          href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
          crossorigin=""/>
@endpush

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
            crossorigin=""></script>
@endpush
