{{-- Componente de visualização de mapa da instituição --}}
@props([
    'institution' => null,
    'lat' => null,
    'lng' => null,
    'zoom' => 16,
    'height' => '400px',
    'label' => 'Localização da Instituição',
])

@php
    $lat = $lat ?? $institution->latitude ?? -14.2350;
    $lng = $lng ?? $institution->longitude ?? -51.9253;
@endphp

<x-forms.maps.base
    mapId="map-institution-show"
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
            const container = document.getElementById('leaflet-container-map-institution-show');
            if (!container) return;

            const lat = parseFloat(container.dataset.lat);
            const lng = parseFloat(container.dataset.lng);
            const zoom = parseInt(container.dataset.zoom || {{ $zoom }});

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

            const map = L.map('map-institution-show', {
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
