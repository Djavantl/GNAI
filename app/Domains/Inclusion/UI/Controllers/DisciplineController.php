<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\UI\Controllers;

use App\Domains\Inclusion\Application\Actions\Disciplines\CreateDisciplineAction;
use App\Domains\Inclusion\Application\Actions\Disciplines\DeleteDisciplineAction;
use App\Domains\Inclusion\Application\Actions\Disciplines\UpdateDisciplineAction;
use App\Domains\Inclusion\Application\Data\Disciplines\CreateDisciplineData;
use App\Domains\Inclusion\Application\Data\Disciplines\ListDisciplinesData;
use App\Domains\Inclusion\Application\Data\Disciplines\UpdateDisciplineData;
use App\Domains\Inclusion\Application\Queries\Disciplines\ListDisciplinesQuery;
use App\Domains\Inclusion\Application\Queries\Disciplines\ShowDisciplineQuery;
use App\Domains\Inclusion\Domain\Models\Discipline;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Throwable;

final class DisciplineController
{
    public function index(ListDisciplinesData $filters, ListDisciplinesQuery $query, Request $request): InertiaResponse
    {
        $disciplines = $query->execute($filters);

        return Inertia::render(
            'Inclusion/Disciplines/Index',
            [
                'disciplines' => $disciplines,
                'filters' => [
                    'name' => $filters->name ?? '',
                    'is_active' => $filters->isActive === null ? '' : (string) (int) $filters->isActive,
                ],
                'can' => [
                    'create' => $request->user()?->can('discipline.create') ?? false,
                    'view' => $request->user()?->can('discipline.view') ?? false,
                    'delete' => $request->user()?->can('discipline.delete') ?? false,
                ],
            ],
        );
    }

    public function show(Discipline $discipline, ShowDisciplineQuery $query, Request $request): InertiaResponse
    {
        $discipline = $query->execute($discipline);

        return Inertia::render(
            'Inclusion/Disciplines/Show',
            [
                'discipline' => $discipline,
                'can' => [
                    'update' => $request->user()?->can('discipline.update') ?? false,
                    'delete' => $request->user()?->can('discipline.delete') ?? false,
                ],
            ],
        );
    }

    public function create(): InertiaResponse
    {
        return Inertia::render('Inclusion/Disciplines/Create');
    }

    public function store(CreateDisciplineData $data, CreateDisciplineAction $action): RedirectResponse
    {
        $action->execute($data);

        return redirect()
            ->route('specialized-educational-support.disciplines.index')
            ->with('success', 'Disciplina cadastrada com sucesso.');
    }

    public function edit(Discipline $discipline): InertiaResponse
    {
        return Inertia::render(
            'Inclusion/Disciplines/Edit',
            compact('discipline'),
        );
    }

    /**
     * @throws Throwable
     */
    public function update(UpdateDisciplineData $data, Discipline $discipline, UpdateDisciplineAction $action): RedirectResponse
    {
        $action->execute($discipline, $data);

        return redirect()
            ->route('specialized-educational-support.disciplines.index')
            ->with('success', 'Disciplina atualizada com sucesso.');
    }

    /**
     * @throws Throwable
     */
    public function destroy(Discipline $discipline, DeleteDisciplineAction $action): RedirectResponse
    {
        $action->execute($discipline);

        return redirect()
            ->route('specialized-educational-support.disciplines.index')
            ->with('success', 'Disciplina removida com sucesso.');
    }
}
