<?php

namespace App\Http\Controllers\InclusiveRadar;

use App\Http\Controllers\Controller;
use App\Http\Requests\InclusiveRadar\LocationRequest;
use App\Models\InclusiveRadar\Institution;
use App\Models\InclusiveRadar\Location;
use App\Services\InclusiveRadar\LocationService;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LocationController extends Controller
{
    public function __construct(
        private LocationService $service,
    ) {}

    public function index(Request $request)
    {
        $locations = Location::with(['institution'])
            ->filterName($request->name)
            ->filterInstitution($request->institution_name)
            ->filterActive($request->is_active)
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        if ($request->ajax()) {
            return view('pages.inclusive-radar.locations.partials.table', compact('locations'))->render();
        }

        return view(
            'pages.inclusive-radar.locations.index',
            compact('locations')
        );
    }

    public function create(): View
    {
        $institutionsQuery = Institution::where('is_active', true)
            ->orderBy('name')
            ->get();

        $institutionsOptions = $institutionsQuery->pluck('name', 'id');

        $institutionsData = $institutionsQuery->mapWithKeys(fn($inst) => [
            $inst->id => [
                'latitude'     => $inst->latitude,
                'longitude'    => $inst->longitude,
                'default_zoom' => $inst->default_zoom ?? 16,
            ]
        ]);

        return view('pages.inclusive-radar.locations.create', [
            'institutions'        => $institutionsOptions,
            'institutionsData'    => $institutionsData,
            'selectedInstitution' => null,
        ]);
    }

    public function store(LocationRequest $request): RedirectResponse
    {
        $this->service->store($request->validated());

        return redirect()
            ->route('inclusive-radar.locations.index')
            ->with('success', 'Ponto de referência criado com sucesso!');
    }

    public function show(Location $location): View
    {
        return view(
            'pages.inclusive-radar.locations.show',
            compact('location')
        );
    }

    public function edit(Location $location): View
    {
        $institutionsQuery = Institution::where('is_active', true)
            ->orderBy('name')
            ->get();

        $institutionsOptions = $institutionsQuery->pluck('name', 'id');

        $institutionsData = $institutionsQuery->mapWithKeys(fn($inst) => [
            $inst->id => [
                'latitude'     => $inst->latitude,
                'longitude'    => $inst->longitude,
                'default_zoom' => $inst->default_zoom ?? 16,
            ]
        ]);

        $selectedInstitution = $institutionsQuery->firstWhere('id', $location->institution_id);

        return view('pages.inclusive-radar.locations.edit', [
            'location'            => $location,
            'institutions'        => $institutionsOptions,
            'institutionsData'    => $institutionsData,
            'selectedInstitution' => $selectedInstitution,
        ]);
    }

    public function update(LocationRequest $request, Location $location): RedirectResponse
    {
        $this->service->update($location, $request->validated());

        return redirect()
            ->route('inclusive-radar.locations.index')
            ->with('success', 'Localização atualizada com sucesso!');
    }

    public function destroy(Location $location): RedirectResponse
    {
        $this->service->delete($location);

        return redirect()
            ->route('inclusive-radar.locations.index')
            ->with('success', 'Localização removida com sucesso!');
    }
}
