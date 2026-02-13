@props([
    'barrier' => null,
    'institution' => null,
    'height' => '400px',
    'label' => 'Localização da Barreira',
])

@php
    // Prioridade: coordenadas da barreira → coordenadas da instituição → fallback
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

            const map = L.map('map-barrier-show').setView([lat, lng], zoom);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap',
                maxZoom: 19
            }).addTo(map);

            // Marcador principal da barreira
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
