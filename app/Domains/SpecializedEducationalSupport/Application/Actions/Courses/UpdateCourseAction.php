<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Actions\Courses;

use App\Domains\SpecializedEducationalSupport\Application\Data\Courses\UpdateCourseData;
use App\Domains\SpecializedEducationalSupport\Application\Queries\Courses\CourseHasStudentsQuery;
use App\Domains\SpecializedEducationalSupport\Domain\DTOs\Courses\UpdateCourseDTO;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Course;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class UpdateCourseAction
{
    public function __construct(
        private CourseHasStudentsQuery $hasStudents,
    ) {}

    /**
     * @throws Throwable
     */
    public function execute(Course $course, UpdateCourseData $data): Course
    {
        return DB::transaction(function () use ($course, $data): Course {
            $lockedCourse = Course::query()
                ->lockForUpdate()
                ->findOrFail($course->getKey());

            if ($lockedCourse->is_active && ! $data->isActive) {
                $lockedCourse->ensureCanBeDeactivated(
                    $this->hasStudents->execute($lockedCourse),
                );
            }

            $courseDTO = new UpdateCourseDTO(
                name: $data->name,
                isActive: $data->isActive,
                description: $data->description,
            );

            $lockedCourse->revise($courseDTO);
            $lockedCourse->save();
            $lockedCourse->assignCurriculum($data->disciplineIds);

            return $lockedCourse->load('disciplines');
        });
    }
}
