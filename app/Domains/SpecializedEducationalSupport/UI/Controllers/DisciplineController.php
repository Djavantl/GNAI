<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\UI\Controllers;

use App\Domains\SpecializedEducationalSupport\Application\Actions\Disciplines\CreateDisciplineAction;
use App\Domains\SpecializedEducationalSupport\Application\Actions\Disciplines\DeleteDisciplineAction;
use App\Domains\SpecializedEducationalSupport\Application\Actions\Disciplines\UpdateDisciplineAction;
use App\Domains\SpecializedEducationalSupport\Application\Data\Disciplines\CreateDisciplineData;
use App\Domains\SpecializedEducationalSupport\Application\Data\Disciplines\ListDisciplinesData;
use App\Domains\SpecializedEducationalSupport\Application\Data\Disciplines\UpdateDisciplineData;
use App\Domains\SpecializedEducationalSupport\Application\Queries\Disciplines\ListDisciplinesQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\Disciplines\ShowDisciplineQuery;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Discipline;
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
            'SpecializedEducationalSupport/Disciplines/Index',
            compact('disciplines'),
        );
    }

    public function show(Discipline $discipline, ShowDisciplineQuery $query): InertiaResponse
    {
        $discipline = $query->execute($discipline);

        return Inertia::render(
            'SpecializedEducationalSupport/Disciplines/Show',
            compact('discipline'),
        );
    }

    public function create(): InertiaResponse
    {
        return Inertia::render('SpecializedEducationalSupport/Disciplines/Create');
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
            'SpecializedEducationalSupport/Disciplines/Edit',
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
