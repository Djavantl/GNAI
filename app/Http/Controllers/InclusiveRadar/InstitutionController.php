<?php

namespace App\Http\Controllers\InclusiveRadar;

use App\Http\Controllers\Controller;
use App\Http\Requests\InclusiveRadar\InstitutionRequest;
use App\Models\InclusiveRadar\Institution;
use App\Services\InclusiveRadar\InstitutionService;
use App\Services\InclusiveRadar\OpenStreetMapService;

class InstitutionController extends Controller
{
    protected InstitutionService $service;
    protected OpenStreetMapService $osmService;

    public function __construct(
        InstitutionService $service,
        OpenStreetMapService $osmService
    ) {
        $this->service = $service;
        $this->osmService = $osmService;
    }

    public function index()
    {
        $institutions = $this->service->listAll();

        return view('inclusive-radar.institutions.index', compact('institutions'));
    }

    public function create()
    {
        return view('inclusive-radar.institutions.create');
    }

    public function store(InstitutionRequest $request)
    {
        $data = $request->validated();

        if (empty($data['latitude']) || empty($data['longitude'])) {
            $address = "{$data['name']}, {$data['city']}, {$data['state']}";
            $coords = $this->osmService->geocode($address);
            if ($coords) {
                $data['latitude'] = $coords['latitude'];
                $data['longitude'] = $coords['longitude'];
            }
        }

        $this->service->store($data);

        return redirect()
            ->route('inclusive-radar.institutions.index')
            ->with('success', 'Instituição criada com sucesso!');
    }

    public function edit(Institution $institution)
    {
        $institution->load('locations');

        return view('inclusive-radar.institutions.edit', compact('institution'));
    }

    public function update(InstitutionRequest $request, Institution $institution)
    {
        $data = $request->validated();

        if (empty($data['latitude']) || empty($data['longitude'])) {
            $address = "{$data['name']}, {$data['city']}, {$data['state']}";
            $coords = $this->osmService->geocode($address);
            if ($coords) {
                $data['latitude'] = $coords['latitude'];
                $data['longitude'] = $coords['longitude'];
            }
        }

        $this->service->update($institution, $data);

        return redirect()
            ->route('inclusive-radar.institutions.index')
            ->with('success', 'Instituição atualizada com sucesso!');
    }

    public function toggleActive(Institution $institution)
    {
        $institution = $this->service->toggleActive($institution);

        $message = $institution->is_active
            ? 'Instituição ativada com sucesso!'
            : 'Instituição desativada com sucesso!';

        return redirect()
            ->route('inclusive-radar.institutions.index')
            ->with('success', $message);
    }

    public function destroy(Institution $institution)
    {
        $this->service->delete($institution);

        return redirect()
            ->route('inclusive-radar.institutions.index')
            ->with('success', 'Instituição removida com sucesso!');
    }
}
