<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Queries\Courses;

use App\Domains\Inclusion\Domain\Models\Course;

final class CourseHasStudentsQuery
{
    public function execute(Course $course): bool
    {
        return $course->studentCourses()->exists();
    }
}
