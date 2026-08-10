<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\UI\Controllers;

use App\Domains\InclusiveRadar\Application\Actions\Locations\CreateLocationAction;
use App\Domains\InclusiveRadar\Application\Actions\Locations\DeleteLocationAction;
use App\Domains\InclusiveRadar\Application\Actions\Locations\UpdateLocationAction;
use App\Domains\InclusiveRadar\Application\Data\Locations\CreateLocationData;
use App\Domains\InclusiveRadar\Application\Data\Locations\ListLocationsData;
use App\Domains\InclusiveRadar\Application\Data\Locations\UpdateLocationData;
use App\Domains\InclusiveRadar\Application\Queries\Locations\ListLocationsQuery;
use App\Domains\InclusiveRadar\Application\Queries\Locations\LocationFormQuery;
use App\Domains\InclusiveRadar\Application\Queries\Locations\ShowLocationQuery;
use App\Domains\InclusiveRadar\Domain\Exceptions\InvalidLocation;
use App\Domains\InclusiveRadar\Domain\Models\Location;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class LocationController
{
    public function index(ListLocationsData $filters, ListLocationsQuery $query, Request $request): View
    {
        $locations = $query->execute($filters);

        if ($request->ajax()) {
            return view(
                'pages.inclusive-radar.locations.partials.table',
                compact('locations'),
            );
        }

        return view(
            'pages.inclusive-radar.locations.index',
            compact('locations'),
        );
    }

    public function create(LocationFormQuery $form, Request $request): View
    {
        return view(
            'pages.inclusive-radar.locations.create',
            $form->execute(
                selectedInstitutionId: $this->selectedInstitutionId($request),
            ),
        );
    }

    /**
     * @throws InvalidLocation
     */
    public function store(CreateLocationData $data, CreateLocationAction $action): RedirectResponse
    {
        $action->execute($data);

        return redirect()
            ->route('inclusive-radar.locations.index')
            ->with('success', 'Ponto de referência criado com sucesso!');
    }

    public function show(Location $location, ShowLocationQuery $query): View
    {
        $location = $query->execute($location);

        return view('pages.inclusive-radar.locations.show', compact('location'));
    }

    public function edit(Location $location, LocationFormQuery $form, Request $request): View
    {
        return view(
            'pages.inclusive-radar.locations.edit',
            $form->execute(
                location: $location,
                selectedInstitutionId: $this->selectedInstitutionId($request),
            ) + ['location' => $location],
        );
    }

    /**
     * @throws InvalidLocation
     * @throws \Throwable
     */
    public function update(UpdateLocationData $data, Location $location, UpdateLocationAction $action): RedirectResponse
    {
        $action->execute($location, $data);

        return redirect()
            ->route('inclusive-radar.locations.index')
            ->with('success', 'Localização atualizada com sucesso!');
    }

    /**
     * @throws \Throwable
     */
    public function destroy(Location $location, DeleteLocationAction $action): RedirectResponse
    {
        $action->execute($location);

        return redirect()
            ->route('inclusive-radar.locations.index')
            ->with('success', 'Localização removida com sucesso!');
    }

    private function selectedInstitutionId(Request $request): ?int
    {
        $institutionId = $request->old('institution_id');

        return $institutionId !== null ? (int) $institutionId : null;
    }
}
