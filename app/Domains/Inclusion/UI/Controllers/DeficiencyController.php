<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\UI\Controllers;

use App\Domains\Inclusion\Application\Actions\Deficiencies\CreateDeficiencyAction;
use App\Domains\Inclusion\Application\Actions\Deficiencies\DeleteDeficiencyAction;
use App\Domains\Inclusion\Application\Actions\Deficiencies\ToggleDeficiencyActiveAction;
use App\Domains\Inclusion\Application\Actions\Deficiencies\UpdateDeficiencyAction;
use App\Domains\Inclusion\Application\Data\Deficiencies\CreateDeficiencyData;
use App\Domains\Inclusion\Application\Data\Deficiencies\ListDeficienciesData;
use App\Domains\Inclusion\Application\Data\Deficiencies\UpdateDeficiencyData;
use App\Domains\Inclusion\Application\Queries\Deficiencies\ListDeficienciesQuery;
use App\Domains\Inclusion\Application\Queries\Deficiencies\ShowDeficiencyQuery;
use App\Domains\Inclusion\Domain\Models\Deficiency;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Throwable;

final class DeficiencyController
{
    public function index(
        ListDeficienciesData $filters,
        ListDeficienciesQuery $query,
        Request $request,
    ): InertiaResponse {
        $deficiencies = $query->execute($filters);

        return Inertia::render(
            'Inclusion/Deficiencies/Index',
            [
                'deficiencies' => $deficiencies,
                'filters' => [
                    'name' => $filters->name ?? '',
                    'cid_code' => $filters->cidCode ?? '',
                    'is_active' => $filters->isActive === null ? '' : (string) (int) $filters->isActive,
                ],
                'can' => [
                    'create' => $request->user()?->can('deficiency.create') ?? false,
                    'view' => $request->user()?->can('deficiency.view') ?? false,
                    'update' => $request->user()?->can('deficiency.update') ?? false,
                    'delete' => $request->user()?->can('deficiency.delete') ?? false,
                ],
            ],
        );
    }

    public function show(Deficiency $deficiency, ShowDeficiencyQuery $query, Request $request): InertiaResponse
    {
        $deficiency = $query->execute($deficiency);

        return Inertia::render(
            'Inclusion/Deficiencies/Show',
            [
                'deficiency' => $deficiency,
                'can' => [
                    'update' => $request->user()?->can('deficiency.update') ?? false,
                    'delete' => $request->user()?->can('deficiency.delete') ?? false,
                ],
            ],
        );
    }

    public function create(): InertiaResponse
    {
        return Inertia::render('Inclusion/Deficiencies/Create');
    }

    public function store(
        CreateDeficiencyData $data,
        CreateDeficiencyAction $action,
    ): RedirectResponse {
        $action->execute($data);

        return redirect()
            ->route('specialized-educational-support.deficiencies.index')
            ->with('success', 'Deficiência criada com sucesso!');
    }

    public function edit(Deficiency $deficiency): InertiaResponse
    {
        return Inertia::render(
            'Inclusion/Deficiencies/Edit',
            compact('deficiency'),
        );
    }

    public function update(
        UpdateDeficiencyData $data,
        Deficiency $deficiency,
        UpdateDeficiencyAction $action,
    ): RedirectResponse {
        $action->execute($deficiency, $data);

        return redirect()
            ->route('specialized-educational-support.deficiencies.index')
            ->with('success', 'Deficiência atualizada com sucesso!');
    }

    /**
     * @throws Throwable
     */
    public function toggleActive(
        Deficiency $deficiency,
        ToggleDeficiencyActiveAction $action,
    ): RedirectResponse {
        $deficiency = $action->execute($deficiency);
        $message = $deficiency->is_active
            ? 'Deficiência ativada com sucesso!'
            : 'Deficiência desativada com sucesso!';

        return redirect()
            ->route('specialized-educational-support.deficiencies.index')
            ->with('success', $message);
    }

    /**
     * @throws Throwable
     */
    public function destroy(
        Deficiency $deficiency,
        DeleteDeficiencyAction $action,
    ): RedirectResponse {
        $action->execute($deficiency);

        return redirect()
            ->route('specialized-educational-support.deficiencies.index')
            ->with('success', 'Deficiência removida!');
    }
}
