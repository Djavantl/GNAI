<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\UI\Controllers;

use App\Domains\Auth\Application\Resolvers\AuthenticatedUserResolver;
use App\Domains\Inclusion\Application\Actions\Pendencies\CompletePendencyAction;
use App\Domains\Inclusion\Application\Actions\Pendencies\CreatePendencyAction;
use App\Domains\Inclusion\Application\Actions\Pendencies\DeletePendencyAction;
use App\Domains\Inclusion\Application\Actions\Pendencies\UpdatePendencyAction;
use App\Domains\Inclusion\Application\Data\Pendencies\CreatePendencyData;
use App\Domains\Inclusion\Application\Data\Pendencies\ListPendenciesData;
use App\Domains\Inclusion\Application\Data\Pendencies\UpdatePendencyData;
use App\Domains\Inclusion\Application\Queries\Pendencies\ListMyPendenciesQuery;
use App\Domains\Inclusion\Application\Queries\Pendencies\ListPendenciesQuery;
use App\Domains\Inclusion\Application\Queries\Pendencies\PendencyFilterOptionsQuery;
use App\Domains\Inclusion\Application\Queries\Pendencies\PendencyFormQuery;
use App\Domains\Inclusion\Application\Queries\Pendencies\ShowPendencyQuery;
use App\Domains\Inclusion\Domain\Models\Pendency;
use App\Domains\Inclusion\UI\Presenters\PendencyListPresenter;
use App\Domains\Inclusion\UI\Presenters\PendencyShowPresenter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Throwable;

final class PendencyController
{
    public function index(
        ListPendenciesData $filters,
        ListPendenciesQuery $query,
        PendencyFilterOptionsQuery $filterOptions,
        PendencyListPresenter $presenter,
        Request $request,
    ): InertiaResponse {
        $pendencies = $query->execute($filters);
        $user = $request->user();

        return Inertia::render('Inclusion/Pendencies/Index', [
            'pendencies' => $pendencies,
            'professionals' => $presenter->professionalOptions($filterOptions->professionals()),
            'priorities' => $presenter->priorityOptions(),
            'filters' => $presenter->filters($filters) + [
                'assigned_to' => $filters->assignedTo === null ? '' : (string) $filters->assignedTo,
            ],
            'can' => [
                'create' => $user?->can('pendency.create') ?? false,
                'view' => $user?->can('pendency.view') ?? false,
                'delete' => $user?->can('pendency.delete') ?? false,
                'my' => ($user?->professional !== null) && ($user?->can('pendency.view') ?? false),
            ],
        ]);
    }

    public function show(
        Pendency $pendency,
        ShowPendencyQuery $query,
        PendencyShowPresenter $presenter,
        Request $request,
    ): InertiaResponse {
        $pendency = $query->execute($pendency);

        return Inertia::render(
            'Inclusion/Pendencies/Show',
            $presenter->present($pendency, $request),
        );
    }

    public function create(PendencyFormQuery $form): InertiaResponse
    {
        return Inertia::render(
            'Inclusion/Pendencies/Create',
            $form->forCreation(),
        );
    }

    /**
     * @throws Throwable
     */
    public function store(
        CreatePendencyData $data,
        CreatePendencyAction $action,
        AuthenticatedUserResolver $authenticatedUser,
        Request $request,
    ): RedirectResponse {
        $action->execute($authenticatedUser->fromRequest($request), $data);

        return redirect()
            ->route('specialized-educational-support.pendencies.index')
            ->with('success', 'Pendência criada com sucesso.');
    }

    public function edit(Pendency $pendency, PendencyFormQuery $form): InertiaResponse
    {
        return Inertia::render(
            'Inclusion/Pendencies/Edit',
            $form->forUpdate($pendency),
        );
    }

    /**
     * @throws Throwable
     */
    public function update(
        UpdatePendencyData $data,
        Pendency $pendency,
        UpdatePendencyAction $action,
        AuthenticatedUserResolver $authenticatedUser,
        Request $request,
    ): RedirectResponse {
        $action->execute($pendency, $data, $authenticatedUser->fromRequest($request));

        return redirect()
            ->route('specialized-educational-support.pendencies.index')
            ->with('success', 'Pendência atualizada com sucesso.');
    }

    /**
     * @throws Throwable
     */
    public function destroy(Pendency $pendency, DeletePendencyAction $action): RedirectResponse
    {
        $action->execute($pendency);

        return redirect()
            ->route('specialized-educational-support.pendencies.index')
            ->with('success', 'Pendência removida com sucesso.');
    }

    public function myPendencies(
        ListPendenciesData $filters,
        ListMyPendenciesQuery $query,
        AuthenticatedUserResolver $authenticatedUser,
        PendencyListPresenter $presenter,
        Request $request,
    ): InertiaResponse {
        $pendencies = $query->execute($filters, $authenticatedUser->fromRequest($request));

        return Inertia::render('Inclusion/Pendencies/My', [
            'pendencies' => $pendencies,
            'priorities' => $presenter->priorityOptions(),
            'filters' => $presenter->filters($filters),
            'can' => [
                'view' => $request->user()?->can('pendency.view') ?? false,
            ],
        ]);
    }

    /**
     * @throws Throwable
     */
    public function markAsCompleted(
        Pendency $pendency,
        CompletePendencyAction $action,
        AuthenticatedUserResolver $authenticatedUser,
        Request $request,
    ): RedirectResponse {
        $action->execute($pendency, $authenticatedUser->fromRequest($request));

        return redirect()
            ->route('specialized-educational-support.pendencies.my')
            ->with('success', 'Pendência completada com sucesso.');
    }
}
