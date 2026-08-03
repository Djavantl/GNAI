<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Actions\StudentCourses;

use App\Domains\SpecializedEducationalSupport\Application\Data\StudentCourses\CreateStudentCourseData;
use App\Domains\SpecializedEducationalSupport\Domain\DTOs\StudentCourses\CreateStudentCourseDTO;
use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidCourse;
use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidStudent;
use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidStudentCourse;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Course;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;
use App\Domains\SpecializedEducationalSupport\Domain\Models\StudentCourse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class CreateStudentCourseAction
{
    /**
     * @throws InvalidCourse
     * @throws InvalidStudent
     * @throws InvalidStudentCourse
     * @throws ModelNotFoundException
     * @throws Throwable
     */
    public function execute(Student $student, CreateStudentCourseData $data): StudentCourse
    {
        return DB::transaction(function () use ($student, $data): StudentCourse {
            $lockedStudent = Student::query()
                ->lockForUpdate()
                ->findOrFail($student->getKey());
            $lockedStudent->ensureIsActive();

            $course = Course::query()
                ->findOrFail($data->courseId);
            $course->ensureIsActive();

            $studentAlreadyHasCourse = StudentCourse::query()
                ->where('student_id', $lockedStudent->getKey())
                ->where('course_id', $course->getKey())
                ->exists();

            if ($studentAlreadyHasCourse) {
                throw new InvalidStudentCourse('Este aluno já possui vínculo com o curso selecionado.');
            }

            if ($data->isCurrent) {
                StudentCourse::query()
                    ->where('student_id', $lockedStudent->getKey())
                    ->where('is_current', true)
                    ->lockForUpdate()
                    ->get()
                    ->each(function (StudentCourse $studentCourse): void {
                        $studentCourse->markAsNotCurrent();
                        $studentCourse->save();
                    });
            }

            $studentCourseDTO = new CreateStudentCourseDTO(
                academicYear: $data->academicYear,
                isCurrent: $data->isCurrent,
            );

            $studentCourse = StudentCourse::register(
                student: $lockedStudent,
                course: $course,
                data: $studentCourseDTO,
            );

            try {
                $studentCourse->save();
            } catch (UniqueConstraintViolationException $exception) {
                throw new InvalidStudentCourse(
                    'Este aluno já possui vínculo com o curso selecionado. Recarregue a página e tente novamente.',
                    previous: $exception,
                );
            }

            return $studentCourse->load(['student.person', 'course']);
        });
    }
}
