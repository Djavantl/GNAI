<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Actions\StudentCourses;

use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidStudent;
use App\Domains\SpecializedEducationalSupport\Domain\Models\StudentCourse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class SetCurrentStudentCourseAction
{
    /**
     * @throws InvalidStudent
     * @throws ModelNotFoundException
     * @throws Throwable
     */
    public function execute(StudentCourse $studentCourse): StudentCourse
    {
        return DB::transaction(function () use ($studentCourse): StudentCourse {
            $lockedStudentCourse = StudentCourse::query()
                ->with('student')
                ->lockForUpdate()
                ->findOrFail($studentCourse->getKey());
            $lockedStudentCourse->student->ensureIsActive();

            StudentCourse::query()
                ->where('student_id', $lockedStudentCourse->student_id)
                ->where('id', '!=', $lockedStudentCourse->getKey())
                ->where('is_current', true)
                ->lockForUpdate()
                ->get()
                ->each(function (StudentCourse $currentStudentCourse): void {
                    $currentStudentCourse->markAsNotCurrent();
                    $currentStudentCourse->save();
                });

            if (! $lockedStudentCourse->is_current) {
                $lockedStudentCourse->markAsCurrent();
                $lockedStudentCourse->save();
            }

            return $lockedStudentCourse->load(['student.person', 'course']);
        });
    }
}
