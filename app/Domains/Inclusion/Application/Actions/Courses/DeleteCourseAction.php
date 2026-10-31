<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Actions\Courses;

use App\Domains\Inclusion\Application\Queries\Courses\CourseHasStudentsQuery;
use App\Domains\Inclusion\Domain\Models\Course;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class DeleteCourseAction
{
    public function __construct(
        private CourseHasStudentsQuery $hasStudents,
    ) {}

    /**
     * @throws Throwable
     */
    public function execute(Course $course): void
    {
        DB::transaction(function () use ($course): void {
            $lockedCourse = Course::query()
                ->lockForUpdate()
                ->findOrFail($course->getKey());

            $lockedCourse->ensureCanBeDeleted(
                $this->hasStudents->execute($lockedCourse),
            );

            $lockedCourse->delete();
        });
    }
}
