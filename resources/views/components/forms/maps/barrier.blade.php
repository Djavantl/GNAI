@props([
    'barrier' => null,
    'institution' => null,
    'lat' => null,
    'lng' => null,
    'zoom' => 16,
])

@php
    if ($lat === null || $lat == 0) {
        if ($barrier) {
            $lat = $barrier->latitude;
            $lng = $barrier->longitude;
        } else if ($institution) {
            $lat = $institution->latitude;
            $lng = $institution->longitude;
        } else {
            $lat = old('latitude', -14.2350);
            $lng = old('longitude', -51.9253);
        }
    }

    if ($lng === null || $lng == 0) {
        if ($barrier) {
            $lng = $barrier->longitude;
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
    mapId="map-barrier"
    :lat="$lat"
    :lng="$lng"
    :zoom="$zoom"
    :height="$attributes->get('height', '450px')"
    :label="$attributes->get('label', 'Localização da Barreira')"
    latId="lat"
    lngId="lng"
    {{ $attributes }}>
</x-forms.maps.base>

@push('scripts')
    <script>
        window.barrierMapConfig = {
            mapId: 'map-barrier',
            lat: {{ $lat }},
            lng: {{ $lng }},
            zoom: {{ $zoom }},
            barrier: @json($barrier ?? null),
            institution: @json($institution ?? null),
            isEditMode: {{ $barrier ? 'true' : 'false' }}
        };
    </script>
    @vite('resources/js/pages/inclusive-radar/barriers.js')
@endpush
