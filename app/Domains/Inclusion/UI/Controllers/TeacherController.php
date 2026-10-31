<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\UI\Controllers;

use App\Domains\Inclusion\Application\Actions\Teachers\CreateTeacherAction;
use App\Domains\Inclusion\Application\Actions\Teachers\DeleteTeacherAction;
use App\Domains\Inclusion\Application\Actions\Teachers\UpdateTeacherAction;
use App\Domains\Inclusion\Application\Actions\Teachers\UpdateTeacherDisciplinesAction;
use App\Domains\Inclusion\Application\Actions\Teachers\UpdateTeacherGlobalPermissionsAction;
use App\Domains\Inclusion\Application\Data\Teachers\CreateTeacherData;
use App\Domains\Inclusion\Application\Data\Teachers\ListTeachersData;
use App\Domains\Inclusion\Application\Data\Teachers\UpdateTeacherData;
use App\Domains\Inclusion\Application\Data\Teachers\UpdateTeacherDisciplinesData;
use App\Domains\Inclusion\Application\Data\Teachers\UpdateTeacherGlobalPermissionsData;
use App\Domains\Inclusion\Application\Queries\Teachers\ListTeachersQuery;
use App\Domains\Inclusion\Application\Queries\Teachers\ShowTeacherQuery;
use App\Domains\Inclusion\Application\Queries\Teachers\TeacherDisciplinesFormQuery;
use App\Domains\Inclusion\Application\Queries\Teachers\TeacherFormQuery;
use App\Domains\Inclusion\Application\Queries\Teachers\TeacherPermissionsFormQuery;
use App\Domains\Inclusion\Domain\Models\Teacher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Throwable;

final class TeacherController
{
    public function index(ListTeachersData $filters, ListTeachersQuery $query, Request $request): InertiaResponse
    {
        $teachers = $query->execute($filters);
        $teachers->through(static function (Teacher $teacher): Teacher {
            $teacher->person?->append('photo_url');

            return $teacher;
        });

        return Inertia::render(
            'Inclusion/Teachers/Index',
            [
                'teachers' => $teachers,
                'filters' => [
                    'name' => $filters->name ?? '',
                    'email' => $filters->email ?? '',
                    'registration' => $filters->registration ?? '',
                ],
                'can' => [
                    'create' => $request->user()?->can('teacher.create') ?? false,
                    'view' => $request->user()?->can('teacher.view') ?? false,
                    'update' => $request->user()?->can('teacher.update') ?? false,
                    'delete' => $request->user()?->can('teacher.delete') ?? false,
                ],
            ],
        );
    }

    public function show(Teacher $teacher, ShowTeacherQuery $query, Request $request): InertiaResponse
    {
        $teacher = $query->execute($teacher);
        $teacher->person?->append('photo_url');
        $isSuperAdmin = $request->user()?->isSuperAdmin() ?? false;

        return Inertia::render(
            'Inclusion/Teachers/Show',
            [
                'teacher' => $teacher,
                'display' => [
                    'createdAt' => $teacher->created_at?->format('d/m/Y'),
                ],
                'impersonationUserId' => $isSuperAdmin
                    ? $teacher->user()->where('is_super_admin', false)->value('id')
                    : null,
                'can' => [
                    'update' => $request->user()?->can('teacher.update') ?? false,
                    'delete' => $request->user()?->can('teacher.delete') ?? false,
                ],
            ],
        );
    }

    public function create(TeacherFormQuery $form): InertiaResponse
    {
        return Inertia::render(
            'Inclusion/Teachers/Create',
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
            ->with('success', $data->password === null
                ? 'Professor cadastrado com sucesso. O link para definição da senha foi enviado por e-mail.'
                : 'Professor cadastrado com sucesso. O acesso já pode ser feito com a senha definida.');
    }

    public function edit(Teacher $teacher, TeacherFormQuery $form): InertiaResponse
    {
        return Inertia::render(
            'Inclusion/Teachers/Edit',
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

    public function permissions(TeacherPermissionsFormQuery $query): InertiaResponse
    {
        return Inertia::render(
            'Inclusion/Teachers/GlobalPermissions',
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

    public function disciplines(Teacher $teacher, TeacherDisciplinesFormQuery $query): InertiaResponse
    {
        return Inertia::render(
            'Inclusion/Teachers/Disciplines',
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
