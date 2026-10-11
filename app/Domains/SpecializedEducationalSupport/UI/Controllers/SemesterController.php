<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\UI\Controllers;

use App\Domains\SpecializedEducationalSupport\Application\Actions\Semesters\CreateSemesterAction;
use App\Domains\SpecializedEducationalSupport\Application\Actions\Semesters\DeleteSemesterAction;
use App\Domains\SpecializedEducationalSupport\Application\Actions\Semesters\SetCurrentSemesterAction;
use App\Domains\SpecializedEducationalSupport\Application\Actions\Semesters\UpdateSemesterAction;
use App\Domains\SpecializedEducationalSupport\Application\Data\Semesters\CreateSemesterData;
use App\Domains\SpecializedEducationalSupport\Application\Data\Semesters\ListSemestersData;
use App\Domains\SpecializedEducationalSupport\Application\Data\Semesters\UpdateSemesterData;
use App\Domains\SpecializedEducationalSupport\Application\Queries\Semesters\ListSemestersQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\Semesters\ShowSemesterQuery;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Semester;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Throwable;

final class SemesterController
{
    public function index(ListSemestersData $filters, ListSemestersQuery $query, Request $request): InertiaResponse
    {
        $semesters = $query->execute($filters);

        return Inertia::render(
            'SpecializedEducationalSupport/Semesters/Index',
            compact('semesters'),
        );
    }

    public function show(Semester $semester, ShowSemesterQuery $query): InertiaResponse
    {
        $semester = $query->execute($semester);

        return Inertia::render(
            'SpecializedEducationalSupport/Semesters/Show',
            compact('semester'),
        );
    }

    public function create(): InertiaResponse
    {
        return Inertia::render('SpecializedEducationalSupport/Semesters/Create');
    }

    /**
     * @throws Throwable
     */
    public function store(CreateSemesterData $data, CreateSemesterAction $action): RedirectResponse
    {
        $action->execute($data);

        return redirect()
            ->route('specialized-educational-support.semesters.index')
            ->with('success', 'Semestre criado com sucesso.');
    }

    public function edit(Semester $semester): InertiaResponse
    {
        return Inertia::render(
            'SpecializedEducationalSupport/Semesters/Edit',
            compact('semester'),
        );
    }

    /**
     * @throws Throwable
     */
    public function update(UpdateSemesterData $data, Semester $semester, UpdateSemesterAction $action): RedirectResponse
    {
        $action->execute($semester, $data);

        return redirect()
            ->route('specialized-educational-support.semesters.index')
            ->with('success', 'Semestre atualizado com sucesso.');
    }

    /**
     * @throws Throwable
     */
    public function setCurrent(Semester $semester, SetCurrentSemesterAction $action): RedirectResponse
    {
        $action->execute($semester);

        return redirect()
            ->route('specialized-educational-support.semesters.index')
            ->with('success', 'Semestre definido como atual.');
    }

    /**
     * @throws Throwable
     */
    public function destroy(Semester $semester, DeleteSemesterAction $action): RedirectResponse
    {
        $action->execute($semester);

        return redirect()
            ->route('specialized-educational-support.semesters.index')
            ->with('success', 'Semestre removido com sucesso.');
    }
}
