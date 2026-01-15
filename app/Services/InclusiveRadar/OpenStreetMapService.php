<?php

namespace App\Services\InclusiveRadar;

use Illuminate\Support\Facades\Http;

class OpenStreetMapService
{
    public function geocode(string $address): ?array
    {
        $response = Http::withHeaders([
            'User-Agent' => config('services.osm.user_agent'),
        ])->get(config('services.osm.api_url'), [
            'q' => $address,
            'format' => 'json',
            'limit' => 1,
        ]);

        $data = $response->json();

        if (!empty($data[0])) {
            return [
                'latitude' => $data[0]['lat'],
                'longitude' => $data[0]['lon'],
            ];
        }

        return null;
    }
}
