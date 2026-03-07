<?php

namespace App\Http\Controllers\InclusiveRadar;

use App\Http\Controllers\Controller;
use App\Http\Requests\InclusiveRadar\LocationRequest;
use App\Models\InclusiveRadar\Location;
use App\Models\InclusiveRadar\Institution;
use App\Services\InclusiveRadar\LocationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LocationController extends Controller
{
    public function __construct(
        protected LocationService $service,
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
        $institutions = Institution::with(['locations' => function ($query) {
            $query->where('is_active', true)
                ->orderBy('name');
        }])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view(
            'pages.inclusive-radar.locations.create',
            compact('institutions')
        );
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
        $institutions = Institution::with([
            'locations' => fn ($q) =>
            $q->where('is_active', true)
                ->orderBy('name')
        ])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view(
            'pages.inclusive-radar.locations.edit',
            compact('location', 'institutions')
        );
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
