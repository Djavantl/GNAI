@props([
    'barrier' => null,
    'institution' => null,
    'height' => '400px',
    'label' => 'Localização da Barreira',
])

@php
    $lat = $barrier->latitude ?? $institution->latitude ?? -14.2350;
    $lng = $barrier->longitude ?? $institution->longitude ?? -51.9253;
    $zoom = $institution->default_zoom ?? 16;
@endphp

<x-forms.maps.base
    mapId="map-barrier-show"
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
            const container = document.getElementById('leaflet-container-map-barrier-show');
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

            const map = L.map('map-barrier-show', {
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
