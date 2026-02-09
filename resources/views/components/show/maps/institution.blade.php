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
            const mapContainer = document.getElementById('leaflet-container-map-institution-show');
            if (!mapContainer) return;

            const lat = parseFloat(mapContainer.dataset.lat);
            const lng = parseFloat(mapContainer.dataset.lng);
            const zoom = parseInt(mapContainer.dataset.zoom || {{ $zoom }});

            // Cria mapa
            const map = L.map('map-institution-show').setView([lat, lng], zoom);

            // Camada base OSM
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap',
                maxZoom: 19
            }).addTo(map);

            // Marcador fixo
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
