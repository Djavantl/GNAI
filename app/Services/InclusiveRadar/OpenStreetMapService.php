<?php

namespace App\Services\InclusiveRadar;

use Illuminate\Support\Facades\Http;

class OpenStreetMapService
{
    public function geocode(string $address): ?array
    {
        /* A API Nominatim exige um User-Agent identificável para evitar bloqueios por
           uso indevido, conforme a Política de Uso Aceitável do OpenStreetMap. */
        $response = Http::withHeaders([
            'User-Agent' => config('services.osm.user_agent'),
        ])->get(config('services.osm.api_url'), [
            'q' => $address,
            'format' => 'json',
            'limit' => 1,
        ]);

        $data = $response->json();

        /* Retornamos apenas o primeiro resultado (limit 1) para simplificar a lógica
           de geolocalização automática durante o cadastro da instituição/local. */
        if (!empty($data[0])) {
            return [
                'latitude' => $data[0]['lat'],
                'longitude' => $data[0]['lon'],
            ];
        }

        return null;
    }
}
