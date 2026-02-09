@props([
    'location' => null,
    'institution' => null,
    'lat' => null,
    'lng' => null,
    'zoom' => 16,
])

@php
    if ($lat === null || $lat == 0) {
        if ($location) {
            $lat = $location->latitude;
            $lng = $location->longitude;
        } else if ($institution) {
            $lat = $institution->latitude;
            $lng = $institution->longitude;
        } else {
            $lat = old('latitude', -14.2350);
            $lng = old('longitude', -51.9253);
        }
    }

    if ($lng === null || $lng == 0) {
        if ($location) {
            $lng = $location->longitude;
        } else if ($institution) {
            $lng = $institution->longitude;
        } else {
            $lng = old('longitude', -51.9253);
        }
    }

    $lat = is_numeric($lat) && $lat != 0 ? $lat : -14.2350;
    $lng = is_numeric($lng) && $lng != 0 ? $lng : -51.9253;
    $zoom = $institution->default_zoom ?? $zoom;
@endphp

<x-forms.maps.base
    mapId="map-location"
    :lat="$lat"
    :lng="$lng"
    :zoom="$zoom"
    :height="$attributes->get('height', '500px')"
    :label="$attributes->get('label', 'Localização no Campus')"
    latId="lat"
    lngId="lng"
    {{ $attributes }}>
</x-forms.maps.base>

@push('scripts')
    <script>
        window.locationMapConfig = {
            mapId: 'map-location',
            lat: {{ $lat }},
            lng: {{ $lng }},
            zoom: {{ $zoom }},
            institution: @json($institution ?? null),
            location: @json($location ?? null),
            isEditMode: {{ $location ? 'true' : 'false' }}
        };

        console.log('Configuração do mapa de localização definida:', window.locationMapConfig);
    </script>
    @vite('resources/js/pages/inclusive-radar/locations.js')
@endpush
