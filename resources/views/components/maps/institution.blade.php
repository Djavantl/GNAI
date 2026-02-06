{{-- resources/views/components/maps/institution.blade.php --}}
@props([
    'institution' => null,
    'lat' => null,
    'lng' => null,
    'zoom' => 16,
])

@php
    // Lógica para determinar lat/lng
    if ($lat === null) {
        $lat = $institution->latitude ?? old('latitude', -14.2350);
    }
    if ($lng === null) {
        $lng = $institution->longitude ?? old('longitude', -51.9253);
    }
@endphp

<x-maps.base
    mapId="map-institution"
    :lat="$lat"
    :lng="$lng"
    :zoom="$zoom"
    :height="$attributes->get('height', '550px')"
    :label="$attributes->get('label', 'Localize a Sede no Mapa')"
    {{ $attributes }}>
</x-maps.base>

@push('scripts')
    <script>
        window.institutionMapConfig = {
            mapId: 'map-institution',
            lat: {{ $lat }},
            lng: {{ $lng }},
            zoom: {{ $zoom }},
            @if($institution)
            institution: @json($institution),
            @endif
            isEditMode: {{ $institution ? 'true' : 'false' }}
        };
    </script>
    @vite('resources/js/pages/inclusive-radar/institutions.js')
@endpush
