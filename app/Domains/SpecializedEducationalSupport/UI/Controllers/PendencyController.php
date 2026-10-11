<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\UI\Controllers;

use App\Domains\Auth\Application\Resolvers\AuthenticatedUserResolver;
use App\Domains\SpecializedEducationalSupport\Application\Actions\Pendencies\CompletePendencyAction;
use App\Domains\SpecializedEducationalSupport\Application\Actions\Pendencies\CreatePendencyAction;
use App\Domains\SpecializedEducationalSupport\Application\Actions\Pendencies\DeletePendencyAction;
use App\Domains\SpecializedEducationalSupport\Application\Actions\Pendencies\UpdatePendencyAction;
use App\Domains\SpecializedEducationalSupport\Application\Data\Pendencies\CreatePendencyData;
use App\Domains\SpecializedEducationalSupport\Application\Data\Pendencies\ListPendenciesData;
use App\Domains\SpecializedEducationalSupport\Application\Data\Pendencies\UpdatePendencyData;
use App\Domains\SpecializedEducationalSupport\Application\Queries\Pendencies\ListMyPendenciesQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\Pendencies\ListPendenciesQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\Pendencies\PendencyFilterOptionsQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\Pendencies\PendencyFormQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\Pendencies\ShowPendencyQuery;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Pendency;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Throwable;

final class PendencyController
{
    public function index(ListPendenciesData $filters, ListPendenciesQuery $query, PendencyFilterOptionsQuery $filterOptions, Request $request): InertiaResponse
    {
        $pendencies = $query->execute($filters);
        $professionals = $filterOptions->professionals();

        return Inertia::render(
            'SpecializedEducationalSupport/Pendencies/Index',
            compact('pendencies', 'professionals'),
        );
    }

    public function show(Pendency $pendency, ShowPendencyQuery $query): InertiaResponse
    {
        $pendency = $query->execute($pendency);

        return Inertia::render(
            'SpecializedEducationalSupport/Pendencies/Show',
            compact('pendency'),
        );
    }

    public function create(PendencyFormQuery $form): InertiaResponse
    {
        return Inertia::render(
            'SpecializedEducationalSupport/Pendencies/Create',
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
            'SpecializedEducationalSupport/Pendencies/Edit',
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
        Request $request,
    ): InertiaResponse {
        $pendencies = $query->execute($filters, $authenticatedUser->fromRequest($request));

        return Inertia::render(
            'SpecializedEducationalSupport/Pendencies/My',
            compact('pendencies'),
        );
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
