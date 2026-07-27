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
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

final class DisciplineController extends Controller
{
    public function index(ListDisciplinesData $filters, ListDisciplinesQuery $query, Request $request): View
    {
        $disciplines = $query->execute($filters);

        if ($request->ajax()) {
            return view(
                'pages.specialized-educational-support.disciplines.partials.table',
                compact('disciplines'),
            );
        }

        return view(
            'pages.specialized-educational-support.disciplines.index',
            compact('disciplines'),
        );
    }

    public function show(Discipline $discipline, ShowDisciplineQuery $query): View
    {
        $discipline = $query->execute($discipline);

        return view(
            'pages.specialized-educational-support.disciplines.show',
            compact('discipline'),
        );
    }

    public function create(): View
    {
        return view('pages.specialized-educational-support.disciplines.create');
    }

    public function store(CreateDisciplineData $data, CreateDisciplineAction $action): RedirectResponse
    {
        $action->execute($data);

        return redirect()
            ->route('specialized-educational-support.disciplines.index')
            ->with('success', 'Disciplina cadastrada com sucesso.');
    }

    public function edit(Discipline $discipline): View
    {
        return view(
            'pages.specialized-educational-support.disciplines.edit',
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
