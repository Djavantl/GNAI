<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\UI\Controllers;

use App\Domains\Inclusion\Application\Actions\Institutions\CreateInstitutionAction;
use App\Domains\Inclusion\Application\Actions\Institutions\DeleteInstitutionAction;
use App\Domains\Inclusion\Application\Actions\Institutions\UpdateInstitutionAction;
use App\Domains\Inclusion\Application\Data\Institutions\CreateInstitutionData;
use App\Domains\Inclusion\Application\Data\Institutions\ListInstitutionsData;
use App\Domains\Inclusion\Application\Data\Institutions\UpdateInstitutionData;
use App\Domains\Inclusion\Application\Queries\Institutions\ListInstitutionsQuery;
use App\Domains\Inclusion\Application\Queries\Institutions\ShowInstitutionQuery;
use App\Domains\Inclusion\Domain\Models\Institution;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Throwable;

final class InstitutionController
{
    public function index(ListInstitutionsData $filters, ListInstitutionsQuery $query, Request $request): InertiaResponse
    {
        $institutions = $query->execute($filters);

        return Inertia::render('Inclusion/Institutions/Index', [
            'institutions' => $institutions,
            'filters' => [
                'name' => $filters->name ?? '',
                'location' => $filters->location ?? '',
                'is_active' => $filters->isActive === null ? '' : (string) (int) $filters->isActive,
            ],
            'can' => [
                'create' => $request->user()?->can('institution.create') ?? false,
                'show' => $request->user()?->can('institution.show') ?? false,
                'destroy' => $request->user()?->can('institution.destroy') ?? false,
            ],
        ]);
    }

    public function create(Request $request): InertiaResponse
    {
        return Inertia::render('Inclusion/Institutions/Create', [
            'canSubmit' => $request->user()?->can('institution.store') ?? false,
        ]);
    }

    /**
     * @throws Throwable
     */
    public function store(CreateInstitutionData $data, CreateInstitutionAction $action): RedirectResponse
    {
        $action->execute($data);

        return redirect()
            ->route('inclusive-radar.institutions.index')
            ->with('success', 'Instituição criada com sucesso!');
    }

    public function show(Institution $institution, ShowInstitutionQuery $query, Request $request): InertiaResponse
    {
        $institution = $query->execute($institution);

        return Inertia::render('Inclusion/Institutions/Show', [
            'institution' => $institution,
            'can' => [
                'edit' => $request->user()?->can('institution.edit') ?? false,
                'destroy' => $request->user()?->can('institution.destroy') ?? false,
            ],
        ]);
    }

    public function edit(Institution $institution, Request $request): InertiaResponse
    {
        $institution->load('locations');

        return Inertia::render('Inclusion/Institutions/Edit', [
            'institution' => $institution,
            'canSubmit' => $request->user()?->can('institution.update') ?? false,
        ]);
    }

    /**
     * @throws Throwable
     */
    public function update(
        UpdateInstitutionData $data,
        Institution $institution,
        UpdateInstitutionAction $action,
    ): RedirectResponse {
        $action->execute($institution, $data);

        return redirect()
            ->route('inclusive-radar.institutions.index')
            ->with('success', 'Instituição atualizada com sucesso!');
    }

    /**
     * @throws Throwable
     */
    public function destroy(Institution $institution, DeleteInstitutionAction $action): RedirectResponse
    {
        $action->execute($institution);

        return redirect()
            ->route('inclusive-radar.institutions.index')
            ->with('success', 'Instituição removida com sucesso!');
    }
}
