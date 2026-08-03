<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\UI\Controllers;

use App\Domains\SpecializedEducationalSupport\Application\Actions\Teachers\CreateTeacherAction;
use App\Domains\SpecializedEducationalSupport\Application\Actions\Teachers\DeleteTeacherAction;
use App\Domains\SpecializedEducationalSupport\Application\Actions\Teachers\UpdateTeacherAction;
use App\Domains\SpecializedEducationalSupport\Application\Actions\Teachers\UpdateTeacherDisciplinesAction;
use App\Domains\SpecializedEducationalSupport\Application\Actions\Teachers\UpdateTeacherGlobalPermissionsAction;
use App\Domains\SpecializedEducationalSupport\Application\Data\Teachers\CreateTeacherData;
use App\Domains\SpecializedEducationalSupport\Application\Data\Teachers\ListTeachersData;
use App\Domains\SpecializedEducationalSupport\Application\Data\Teachers\UpdateTeacherData;
use App\Domains\SpecializedEducationalSupport\Application\Data\Teachers\UpdateTeacherDisciplinesData;
use App\Domains\SpecializedEducationalSupport\Application\Data\Teachers\UpdateTeacherGlobalPermissionsData;
use App\Domains\SpecializedEducationalSupport\Application\Queries\Teachers\ListTeachersQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\Teachers\ShowTeacherQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\Teachers\TeacherDisciplinesFormQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\Teachers\TeacherFormQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\Teachers\TeacherPermissionsFormQuery;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Teacher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

final class TeacherController
{
    public function index(ListTeachersData $filters, ListTeachersQuery $query, Request $request): View
    {
        $teachers = $query->execute($filters);

        if ($request->ajax()) {
            return view(
                'pages.specialized-educational-support.teachers.partials.table',
                compact('teachers'),
            );
        }

        return view(
            'pages.specialized-educational-support.teachers.index',
            compact('teachers'),
        );
    }

    public function show(Teacher $teacher, ShowTeacherQuery $query): View
    {
        $teacher = $query->execute($teacher);

        return view(
            'pages.specialized-educational-support.teachers.show',
            compact('teacher'),
        );
    }

    public function create(TeacherFormQuery $form): View
    {
        return view(
            'pages.specialized-educational-support.teachers.create',
            $form->forCreation(),
        );
    }

    /**
     * @throws Throwable
     */
    public function store(CreateTeacherData $data, CreateTeacherAction $action): RedirectResponse
    {
        $action->execute($data);

        return redirect()
            ->route('specialized-educational-support.teachers.index')
            ->with('success', 'Professor cadastrado com sucesso.');
    }

    public function edit(Teacher $teacher, TeacherFormQuery $form): View
    {
        return view(
            'pages.specialized-educational-support.teachers.edit',
            $form->forUpdate($teacher),
        );
    }

    /**
     * @throws Throwable
     */
    public function update(UpdateTeacherData $data, Teacher $teacher, UpdateTeacherAction $action): RedirectResponse
    {
        $action->execute($teacher, $data);

        return redirect()
            ->route('specialized-educational-support.teachers.index')
            ->with('success', 'Professor atualizado com sucesso.');
    }

    /**
     * @throws Throwable
     */
    public function destroy(Teacher $teacher, DeleteTeacherAction $action): RedirectResponse
    {
        $action->execute($teacher);

        return redirect()
            ->route('specialized-educational-support.teachers.index')
            ->with('success', 'Professor removido com sucesso.');
    }

    public function permissions(TeacherPermissionsFormQuery $query): View
    {
        return view(
            'pages.specialized-educational-support.teachers.global-permissions',
            $query->execute(),
        );
    }

    /**
     * @throws Throwable
     */
    public function updatePermissions(
        UpdateTeacherGlobalPermissionsData $data,
        UpdateTeacherGlobalPermissionsAction $action,
    ): RedirectResponse {
        $action->execute($data);

        return redirect()
            ->route('specialized-educational-support.teachers.index')
            ->with('success', 'Permissões globais de professores atualizadas!');
    }

    public function disciplines(Teacher $teacher, TeacherDisciplinesFormQuery $query): View
    {
        return view(
            'pages.specialized-educational-support.teachers.disciplines',
            $query->execute($teacher),
        );
    }

    /**
     * @throws Throwable
     */
    public function updateDisciplines(
        UpdateTeacherDisciplinesData $data,
        Teacher $teacher,
        UpdateTeacherDisciplinesAction $action,
    ): RedirectResponse {
        $action->execute($teacher, $data);

        return redirect()
            ->route('specialized-educational-support.teachers.show', $teacher)
            ->with('success', 'Matriz curricular atualizada com sucesso!');
    }
}
