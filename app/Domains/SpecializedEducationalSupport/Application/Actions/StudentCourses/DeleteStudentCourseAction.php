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
            $studentId = (int) $lockedStudentCourse->student_id;
            $wasCurrent = (bool) $lockedStudentCourse->is_current;

            $lockedStudentCourse->delete();

            if (! $wasCurrent) {
                return;
            }

            $previousStudentCourse = StudentCourse::query()
                ->where('student_id', $studentId)
                ->orderByDesc('academic_year')
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->lockForUpdate()
                ->first();

            if ($previousStudentCourse !== null) {
                $previousStudentCourse->markAsCurrent();
                $previousStudentCourse->save();
            }
        });
    }
}
