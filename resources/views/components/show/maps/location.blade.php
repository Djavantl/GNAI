@props([
    'location' => null,
    'institution' => null,
    'height' => '400px',
    'label' => 'Localização'
])

@php
    $lat = $location->latitude ?? $institution->latitude ?? -14.2350;
    $lng = $location->longitude ?? $institution->longitude ?? -51.9253;
    $zoom = $institution->default_zoom ?? 16;
@endphp

<x-forms.maps.base
    mapId="map-location-show"
    :lat="$lat"
    :lng="$lng"
    :zoom="$zoom"
    :height="$height"
    :label="$label"
    :showLegend="false"
/>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const container = document.getElementById('leaflet-container-map-location-show');
            if (!container) return;

            const lat = parseFloat(container.dataset.lat);
            const lng = parseFloat(container.dataset.lng);
            const zoom = parseInt(container.dataset.zoom);

            const streetLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap',
                maxZoom: 19
            });

            const satelliteLayer = L.tileLayer(
                'https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}',
                {
                    attribution: '© Google',
                    maxZoom: 21
                }
            );

            const map = L.map('map-location-show', {
                center: [lat, lng],
                zoom: zoom,
                layers: [streetLayer]
            });

            const baseMaps = {
                "Mapa de Ruas": streetLayer,
                "Satélite": satelliteLayer
            };

            L.control.layers(baseMaps).addTo(map);

            L.marker([lat, lng])
                .addTo(map)
                .bindTooltip('{{ $label }}', {
                    permanent: true,
                    direction: 'top',
                    offset: [-15, -7],
                    className: 'bg-primary text-white fw-bold px-2 rounded shadow-sm'
                });

        });
    </script>
@endpush
