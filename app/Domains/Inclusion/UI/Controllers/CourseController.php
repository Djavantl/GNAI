<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\UI\Controllers;

use App\Domains\Inclusion\Application\Actions\Courses\CreateCourseAction;
use App\Domains\Inclusion\Application\Actions\Courses\DeleteCourseAction;
use App\Domains\Inclusion\Application\Actions\Courses\UpdateCourseAction;
use App\Domains\Inclusion\Application\Data\Courses\CreateCourseData;
use App\Domains\Inclusion\Application\Data\Courses\ListCoursesData;
use App\Domains\Inclusion\Application\Data\Courses\UpdateCourseData;
use App\Domains\Inclusion\Application\Queries\Courses\CourseFormQuery;
use App\Domains\Inclusion\Application\Queries\Courses\ListCoursesQuery;
use App\Domains\Inclusion\Application\Queries\Courses\ShowCourseQuery;
use App\Domains\Inclusion\Domain\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Throwable;

final class CourseController
{
    public function index(ListCoursesData $filters, ListCoursesQuery $query, Request $request): InertiaResponse
    {
        $courses = $query->execute($filters);

        return Inertia::render(
            'Inclusion/Courses/Index',
            [
                'courses' => $courses,
                'filters' => [
                    'name' => $filters->name ?? '',
                    'is_active' => $filters->isActive === null ? '' : (string) (int) $filters->isActive,
                ],
                'can' => [
                    'create' => $request->user()?->can('course.create') ?? false,
                    'view' => $request->user()?->can('course.view') ?? false,
                    'delete' => $request->user()?->can('course.delete') ?? false,
                ],
            ],
        );
    }

    public function show(Course $course, ShowCourseQuery $query, Request $request): InertiaResponse
    {
        $course = $query->execute($course);

        return Inertia::render(
            'Inclusion/Courses/Show',
            [
                'course' => $course,
                'can' => [
                    'update' => $request->user()?->can('course.update') ?? false,
                    'delete' => $request->user()?->can('course.delete') ?? false,
                ],
            ],
        );
    }

    public function create(CourseFormQuery $form): InertiaResponse
    {
        return Inertia::render(
            'Inclusion/Courses/Create',
            $form->forCreation(),
        );
    }

    /**
     * @throws Throwable
     */
    public function store(CreateCourseData $data, CreateCourseAction $action): RedirectResponse
    {
        $action->execute($data);

        return redirect()
            ->route('specialized-educational-support.courses.index')
            ->with('success', 'Curso cadastrado com sucesso.');
    }

    public function edit(Course $course, CourseFormQuery $form): InertiaResponse
    {
        return Inertia::render(
            'Inclusion/Courses/Edit',
            $form->forUpdate($course),
        );
    }

    /**
     * @throws Throwable
     */
    public function update(UpdateCourseData $data, Course $course, UpdateCourseAction $action): RedirectResponse
    {
        $action->execute($course, $data);

        return redirect()
            ->route('specialized-educational-support.courses.index')
            ->with('success', 'Curso atualizado com sucesso.');
    }

    /**
     * @throws Throwable
     */
    public function destroy(Course $course, DeleteCourseAction $action): RedirectResponse
    {
        $action->execute($course);

        return redirect()
            ->route('specialized-educational-support.courses.index')
            ->with('success', 'Curso removido com sucesso.');
    }
}
