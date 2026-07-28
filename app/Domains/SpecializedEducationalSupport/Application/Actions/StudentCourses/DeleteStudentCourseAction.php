<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Actions\StudentCourses;

use App\Domains\SpecializedEducationalSupport\Domain\Models\StudentCourse;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class DeleteStudentCourseAction
{
    /**
     * @throws Throwable
     */
    public function execute(StudentCourse $studentCourse): void
    {
        DB::transaction(function () use ($studentCourse): void {
            $lockedStudentCourse = StudentCourse::query()
                ->lockForUpdate()
                ->findOrFail($studentCourse->getKey());

            $lockedStudentCourse->delete();
        });
    }
}
