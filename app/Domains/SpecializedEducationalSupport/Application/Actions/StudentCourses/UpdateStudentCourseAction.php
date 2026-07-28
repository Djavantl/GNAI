<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Actions\StudentCourses;

use App\Domains\SpecializedEducationalSupport\Application\Data\StudentCourses\UpdateStudentCourseData;
use App\Domains\SpecializedEducationalSupport\Domain\DTOs\StudentCourses\UpdateStudentCourseDTO;
use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidCourse;
use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidStudent;
use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidStudentCourse;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Course;
use App\Domains\SpecializedEducationalSupport\Domain\Models\StudentCourse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class UpdateStudentCourseAction
{
    /**
     * @throws InvalidCourse
     * @throws InvalidStudent
     * @throws InvalidStudentCourse
     * @throws ModelNotFoundException
     * @throws Throwable
     */
    public function execute(StudentCourse $studentCourse, UpdateStudentCourseData $data): StudentCourse
    {
        return DB::transaction(function () use ($studentCourse, $data): StudentCourse {
            $lockedStudentCourse = StudentCourse::query()
                ->with('student')
                ->lockForUpdate()
                ->findOrFail($studentCourse->getKey());
            $lockedStudentCourse->student->ensureIsActive();

            $lockedCourse = Course::query()
                ->lockForUpdate()
                ->findOrFail($data->courseId);
            $lockedCourse->ensureIsActive();

            $studentAlreadyHasCourse = StudentCourse::query()
                ->where('student_id', $lockedStudentCourse->student_id)
                ->where('course_id', $lockedCourse->getKey())
                ->where('id', '!=', $lockedStudentCourse->getKey())
                ->exists();

            if ($studentAlreadyHasCourse) {
                throw new InvalidStudentCourse('Este aluno já possui vínculo com o curso selecionado.');
            }

            if ($data->isCurrent && ! $lockedStudentCourse->is_current) {
                StudentCourse::query()
                    ->where('student_id', $lockedStudentCourse->student_id)
                    ->where('is_current', true)
                    ->where('id', '!=', $lockedStudentCourse->getKey())
                    ->lockForUpdate()
                    ->get()
                    ->each(function (StudentCourse $studentCourse): void {
                        $studentCourse->markAsNotCurrent();
                        $studentCourse->save();
                    });
            }

            $studentCourseDTO = new UpdateStudentCourseDTO(
                academicYear: $data->academicYear,
                isCurrent: $data->isCurrent,
            );

            $lockedStudentCourse->revise(
                course: $lockedCourse,
                data: $studentCourseDTO,
            );

            try {
                $lockedStudentCourse->save();
            } catch (UniqueConstraintViolationException $exception) {
                throw new InvalidStudentCourse(
                    'Este aluno já possui vínculo com o curso selecionado. Recarregue a página e tente novamente.',
                    previous: $exception,
                );
            }

            return $lockedStudentCourse->load(['student.person', 'course']);
        });
    }
}
