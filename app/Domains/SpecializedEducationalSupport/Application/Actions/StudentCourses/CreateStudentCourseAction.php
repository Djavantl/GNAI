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

            $existingStudentCourses = StudentCourse::query()
                ->where('student_id', $lockedStudent->getKey())
                ->lockForUpdate()
                ->get();
            $studentAlreadyHasCourse = $existingStudentCourses->contains(
                static fn (StudentCourse $studentCourse): bool => (int) $studentCourse->course_id === (int) $course->getKey()
            );

            if ($studentAlreadyHasCourse) {
                throw new InvalidStudentCourse('Este aluno já possui vínculo com o curso selecionado.');
            }

            $isCurrent = $data->isCurrent || $existingStudentCourses->isEmpty();

            if ($isCurrent) {
                $existingStudentCourses
                    ->where('is_current', true)
                    ->each(function (StudentCourse $studentCourse): void {
                        $studentCourse->markAsNotCurrent();
                        $studentCourse->save();
                    });
            }

            $studentCourseDTO = new CreateStudentCourseDTO(
                academicYear: $data->academicYear,
                isCurrent: $isCurrent,
                schoolAttendanceStatus: $isCurrent ? $data->schoolAttendanceStatus : null,
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

            if ($isCurrent) {
                $this->ensureDisciplinesBelongToCourse($course, $data->failedDisciplineIds, $data->atRiskDisciplineIds);
                $studentCourse->syncFailedDisciplines($data->failedDisciplineIds);
                $studentCourse->syncAtRiskDisciplines($data->atRiskDisciplineIds);
            }

            return $studentCourse->load(['student.person', 'course', 'failedDisciplines', 'atRiskDisciplines']);
        });
    }

    /** @param list<int> $failedIds @param list<int> $atRiskIds */
    private function ensureDisciplinesBelongToCourse(Course $course, array $failedIds, array $atRiskIds): void
    {
        $allowedIds = $course->disciplines()->pluck('disciplines.id')->map(static fn (mixed $id): int => (int) $id)->all();

        if (array_diff(array_map('intval', [...$failedIds, ...$atRiskIds]), $allowedIds) !== []) {
            throw new InvalidStudentCourse('As disciplinas selecionadas devem pertencer ao curso do aluno.');
        }
    }
}
