<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Actions\Courses;

use App\Domains\SpecializedEducationalSupport\Application\Data\Courses\CreateCourseData;
use App\Domains\SpecializedEducationalSupport\Domain\DTOs\Courses\CreateCourseDTO;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Course;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class CreateCourseAction
{
    /**
     * @throws Throwable
     */
    public function execute(CreateCourseData $data): Course
    {
        return DB::transaction(function () use ($data): Course {
            $courseDTO = new CreateCourseDTO(
                name: $data->name,
                description: $data->description,
            );

            $course = Course::register($courseDTO);
            $course->save();
            $course->assignCurriculum($data->disciplineIds);

            return $course->load('disciplines');
        });
    }
}
