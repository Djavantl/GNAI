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
use App\Shared\Infrastructure\Security\RichTextSanitizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

final class LocationController
{
    public function index(ListLocationsData $filters, ListLocationsQuery $query, Request $request): InertiaResponse
    {
        $locations = $query->execute($filters);

        return Inertia::render(
            'InclusiveRadar/Locations/Index',
            [
                'locations' => $locations,
                'filters' => [
                    'name' => $filters->name ?? '',
                    'institution_name' => $filters->institutionName ?? '',
                    'is_active' => $filters->isActive === null ? '' : (string) (int) $filters->isActive,
                ],
                'can' => [
                    'create' => $request->user()?->can('location.create') ?? false,
                    'show' => $request->user()?->can('location.show') ?? false,
                    'destroy' => $request->user()?->can('location.destroy') ?? false,
                ],
            ],
        );
    }

    public function create(LocationFormQuery $form, Request $request): InertiaResponse
    {
        return Inertia::render(
            'InclusiveRadar/Locations/Create',
            $form->execute(selectedInstitutionId: $this->selectedInstitutionId($request)) + [
                'canSubmit' => $request->user()?->can('location.store') ?? false,
            ],
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

    public function show(Location $location, ShowLocationQuery $query, Request $request): InertiaResponse
    {
        $location = $query->execute($location);

        return Inertia::render('InclusiveRadar/Locations/Show', [
            'location' => $location,
            'display' => [
                'descriptionHtml' => RichTextSanitizer::sanitize($location->description ?: '— Não informada —'),
            ],
            'can' => [
                'edit' => $request->user()?->can('location.edit') ?? false,
                'destroy' => $request->user()?->can('location.destroy') ?? false,
            ],
        ]);
    }

    public function edit(Location $location, LocationFormQuery $form, Request $request): InertiaResponse
    {
        return Inertia::render(
            'InclusiveRadar/Locations/Edit',
            $form->execute(
                location: $location,
                selectedInstitutionId: $this->selectedInstitutionId($request),
            ) + [
                'location' => $location,
                'canSubmit' => $request->user()?->can('location.update') ?? false,
            ],
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
