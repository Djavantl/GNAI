<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\UI\Controllers;

use App\Domains\Inclusion\Application\Actions\Locations\CreateLocationAction;
use App\Domains\Inclusion\Application\Actions\Locations\DeleteLocationAction;
use App\Domains\Inclusion\Application\Actions\Locations\UpdateLocationAction;
use App\Domains\Inclusion\Application\Data\Locations\CreateLocationData;
use App\Domains\Inclusion\Application\Data\Locations\ListLocationsData;
use App\Domains\Inclusion\Application\Data\Locations\UpdateLocationData;
use App\Domains\Inclusion\Application\Queries\Locations\ListLocationsQuery;
use App\Domains\Inclusion\Application\Queries\Locations\LocationFormQuery;
use App\Domains\Inclusion\Application\Queries\Locations\ShowLocationQuery;
use App\Domains\Inclusion\Domain\Exceptions\InvalidLocation;
use App\Domains\Inclusion\Domain\Models\Location;
use App\Shared\Infrastructure\Security\RichTextSanitizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Throwable;

final class LocationController
{
    public function index(ListLocationsData $filters, ListLocationsQuery $query, Request $request): InertiaResponse
    {
        $locations = $query->execute($filters);

        return Inertia::render('Inclusion/Locations/Index', [
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
        ]);
    }

    public function create(LocationFormQuery $form, Request $request): InertiaResponse
    {
        return Inertia::render(
            'Inclusion/Locations/Create',
            $form->execute(
                selectedInstitutionId: $request->old('institution_id') !== null
                    ? (int) $request->old('institution_id')
                    : null,
            ) + ['canSubmit' => $request->user()?->can('location.store') ?? false],
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

        return Inertia::render('Inclusion/Locations/Show', [
            'location' => $location,
            'display' => [
                'descriptionHtml' => RichTextSanitizer::sanitize($location->description ?: 'Não informada'),
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
            'Inclusion/Locations/Edit',
            $form->execute(
                location: $location,
                selectedInstitutionId: $request->old('institution_id') !== null
                    ? (int) $request->old('institution_id')
                    : null,
            ) + [
                'location' => $location,
                'canSubmit' => $request->user()?->can('location.update') ?? false,
            ],
        );
    }

    /**
     * @throws InvalidLocation
     * @throws Throwable
     */
    public function update(UpdateLocationData $data, Location $location, UpdateLocationAction $action): RedirectResponse
    {
        $action->execute($location, $data);

        return redirect()
            ->route('inclusive-radar.locations.index')
            ->with('success', 'Localização atualizada com sucesso!');
    }

    /**
     * @throws Throwable
     */
    public function destroy(Location $location, DeleteLocationAction $action): RedirectResponse
    {
        $action->execute($location);

        return redirect()
            ->route('inclusive-radar.locations.index')
            ->with('success', 'Localização removida com sucesso!');
    }
}
