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
use Illuminate\View\View;
use Throwable;

final class SemesterController
{
    public function index(ListSemestersData $filters, ListSemestersQuery $query, Request $request): View
    {
        $semesters = $query->execute($filters);

        if ($request->ajax()) {
            return view(
                'pages.specialized-educational-support.semesters.partials.table',
                compact('semesters'),
            );
        }

        return view(
            'pages.specialized-educational-support.semesters.index',
            compact('semesters'),
        );
    }

    public function show(Semester $semester, ShowSemesterQuery $query): View
    {
        $semester = $query->execute($semester);

        return view(
            'pages.specialized-educational-support.semesters.show',
            compact('semester'),
        );
    }

    public function create(): View
    {
        return view('pages.specialized-educational-support.semesters.create');
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

    public function edit(Semester $semester): View
    {
        return view(
            'pages.specialized-educational-support.semesters.edit',
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
