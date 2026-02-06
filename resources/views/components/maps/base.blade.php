{{-- resources/views/components/maps/base.blade.php --}}
@props([
    'mapId' => 'map',
    'lat' => -14.2350,
    'lng' => -51.9253,
    'zoom' => 16,
    'height' => '500px',
    'latId' => 'lat',
    'lngId' => 'lng',
    'label' => 'Localização no Mapa',
    'showLegend' => true,
])

<div {{ $attributes->merge(['class' => 'leaflet-map-container']) }}
     id="leaflet-container-{{ $mapId }}"
     data-lat="{{ old('latitude', $lat) }}"
     data-lng="{{ old('longitude', $lng) }}"
     data-zoom="{{ old('default_zoom', $zoom) }}"
     data-lat-id="{{ $latId }}"
     data-lng-id="{{ $lngId }}">

    {{-- Título --}}
    <div class="d-flex justify-content-between align-items-center mb-1">
        <span
            id="map-label-{{ $mapId }}"
            class="form-label fw-bold text-purple-dark mb-0"
        >
            {{ $label }}
        </span>
        <small
            id="map-help-{{ $mapId }}"
            class="text-muted italic"
            style="font-size: 0.75rem;"
        >
            Clique no mapa para definir o ponto
        </small>
    </div>

    {{-- Mapa --}}
    <div class="map-wrapper shadow-sm">
        <div
            id="{{ $mapId }}"
            style="height: {{ $height }};"
            role="application"
            aria-labelledby="map-label-{{ $mapId }}"
            aria-describedby="map-help-{{ $mapId }}">
        </div>
    </div>

    {{-- Display de coordenadas --}}
    <div class="d-flex gap-3 mt-2">
        <div class="small text-muted">
            <span class="fw-bold text-purple-dark">LAT:</span>
            <span id="display-{{ $mapId }}-lat">{{ old('latitude', $lat) }}</span>
        </div>
        <div class="small text-muted">
            <span class="fw-bold text-purple-dark">LNG:</span>
            <span id="display-{{ $mapId }}-lng">{{ old('longitude', $lng) }}</span>
        </div>
    </div>

    {{-- Inputs ocultos --}}
    <input type="hidden" name="latitude" id="{{ $latId }}" value="{{ old('latitude', $lat) }}">
    <input type="hidden" name="longitude" id="{{ $lngId }}" value="{{ old('longitude', $lng) }}">

    {{-- Legenda --}}
    @if($showLegend)
        <div id="map-legend-{{ $mapId }}" class="map-legend-container d-none mb-3">
            {{-- A legenda será preenchida pelo JavaScript --}}
        </div>
    @endif
</div>

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endpush
