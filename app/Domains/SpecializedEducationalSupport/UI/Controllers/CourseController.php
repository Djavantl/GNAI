<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\UI\Controllers;

use App\Domains\SpecializedEducationalSupport\Application\Actions\Courses\CreateCourseAction;
use App\Domains\SpecializedEducationalSupport\Application\Actions\Courses\DeleteCourseAction;
use App\Domains\SpecializedEducationalSupport\Application\Actions\Courses\UpdateCourseAction;
use App\Domains\SpecializedEducationalSupport\Application\Data\Courses\CreateCourseData;
use App\Domains\SpecializedEducationalSupport\Application\Data\Courses\ListCoursesData;
use App\Domains\SpecializedEducationalSupport\Application\Data\Courses\UpdateCourseData;
use App\Domains\SpecializedEducationalSupport\Application\Queries\Courses\CourseFormQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\Courses\ListCoursesQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\Courses\ShowCourseQuery;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Course;
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
            'SpecializedEducationalSupport/Courses/Index',
            compact('courses'),
        );
    }

    public function show(Course $course, ShowCourseQuery $query): InertiaResponse
    {
        $course = $query->execute($course);

        return Inertia::render(
            'SpecializedEducationalSupport/Courses/Show',
            compact('course'),
        );
    }

    public function create(CourseFormQuery $form): InertiaResponse
    {
        return Inertia::render(
            'SpecializedEducationalSupport/Courses/Create',
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
            'SpecializedEducationalSupport/Courses/Edit',
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
