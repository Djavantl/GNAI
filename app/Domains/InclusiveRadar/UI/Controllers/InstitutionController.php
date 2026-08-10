<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\UI\Controllers;

use App\Domains\InclusiveRadar\Application\Actions\Institutions\CreateInstitutionAction;
use App\Domains\InclusiveRadar\Application\Actions\Institutions\DeleteInstitutionAction;
use App\Domains\InclusiveRadar\Application\Actions\Institutions\UpdateInstitutionAction;
use App\Domains\InclusiveRadar\Application\Data\Institutions\CreateInstitutionData;
use App\Domains\InclusiveRadar\Application\Data\Institutions\ListInstitutionsData;
use App\Domains\InclusiveRadar\Application\Data\Institutions\UpdateInstitutionData;
use App\Domains\InclusiveRadar\Application\Queries\Institutions\ListInstitutionsQuery;
use App\Domains\InclusiveRadar\Application\Queries\Institutions\ShowInstitutionQuery;
use App\Domains\InclusiveRadar\Domain\Models\Institution;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

final class InstitutionController
{
    public function index(ListInstitutionsData $filters, ListInstitutionsQuery $query, Request $request): View
    {
        $institutions = $query->execute($filters);

        if ($request->ajax()) {
            return view(
                'pages.inclusive-radar.institutions.partials.table',
                compact('institutions'),
            );
        }

        return view(
            'pages.inclusive-radar.institutions.index',
            compact('institutions'),
        );
    }

    public function create(): View
    {
        return view('pages.inclusive-radar.institutions.create');
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

    public function show(Institution $institution, ShowInstitutionQuery $query): View
    {
        $institution = $query->execute($institution);

        return view(
            'pages.inclusive-radar.institutions.show',
            compact('institution'),
        );
    }

    public function edit(Institution $institution): View
    {
        $institution->load('locations');

        return view(
            'pages.inclusive-radar.institutions.edit',
            compact('institution'),
        );
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
