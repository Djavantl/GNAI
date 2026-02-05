<?php

namespace App\Http\Controllers\InclusiveRadar;

use App\Http\Controllers\Controller;
use App\Http\Requests\InclusiveRadar\LocationRequest;
use App\Models\InclusiveRadar\Location;
use App\Models\InclusiveRadar\Institution;
use App\Services\InclusiveRadar\LocationService;
use App\Services\InclusiveRadar\OpenStreetMapService;

class LocationController extends Controller
{
    protected LocationService $service;
    protected OpenStreetMapService $osmService;

    public function __construct(LocationService $service, OpenStreetMapService $osmService)
    {
        $this->service = $service;
        $this->osmService = $osmService;
    }

    public function index()
    {
        $locations = $this->service->listAll();

        return view('pages.inclusive-radar.locations.index', compact('locations'));
    }

    public function create()
    {
        $institutions = $this->service->getActiveInstitutionsWithLocations();

        return view('pages.inclusive-radar.locations.create', compact('institutions'));
    }

    public function store(LocationRequest $request)
    {
        $data = $request->validated();

        if (empty($data['latitude']) || empty($data['longitude'])) {
            $institution = Institution::find($data['institution_id']);
            $address = "{$data['name']}, {$institution->city}, {$institution->state}";
            $coords = $this->osmService->geocode($address);

            if ($coords) {
                $data['latitude'] = $coords['latitude'];
                $data['longitude'] = $coords['longitude'];
            } else {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['latitude' => 'Não foi possível determinar a localização. Por favor, clique no mapa.']);
            }
        }

        $this->service->store($data);

        return redirect()
            ->route('inclusive-radar.locations.index')
            ->with('success', 'Ponto de referência criado com sucesso!');
    }

    public function edit(Location $location)
    {
        $institutions = $this->service->getActiveInstitutionsWithLocations();

        return view('pages.inclusive-radar.locations.edit', compact('location', 'institutions'));
    }


    public function update(LocationRequest $request, Location $location)
    {
        $data = $request->validated();

        if (empty($data['latitude']) || empty($data['longitude'])) {
            $institution = Institution::find($data['institution_id']);
            $address = "{$data['name']}, {$institution->city}, {$institution->state}";
            $coords = $this->osmService->geocode($address);
            if ($coords) {
                $data['latitude'] = $coords['latitude'];
                $data['longitude'] = $coords['longitude'];
            }
        }

        $this->service->update($location, $data);

        return redirect()
            ->route('inclusive-radar.locations.index')
            ->with('success', 'Localização atualizada com sucesso!');
    }

    public function toggleActive(Location $location)
    {
        $location = $this->service->toggleActive($location);

        $message = $location->is_active
            ? 'Localização ativada com sucesso!'
            : 'Localização desativada com sucesso!';

        return redirect()
            ->route('inclusive-radar.locations.index')
            ->with('success', $message);
    }

    public function destroy(Location $location)
    {
        $this->service->delete($location);

        return redirect()
            ->route('inclusive-radar.locations.index')
            ->with('success', 'Localização removida com sucesso!');
    }
}
