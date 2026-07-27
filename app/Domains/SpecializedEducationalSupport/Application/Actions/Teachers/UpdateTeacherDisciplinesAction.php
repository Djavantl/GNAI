<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Actions\Teachers;

use App\Domains\SpecializedEducationalSupport\Application\Data\Teachers\UpdateTeacherDisciplinesData;
use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidTeacher;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Course;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Teacher;
use App\Models\SpecializedEducationalSupport\TeacherCourseDiscipline;
use DomainException;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class UpdateTeacherDisciplinesAction
{
    /**
     * @throws InvalidTeacher
     * @throws Throwable
     */
    public function execute(Teacher $teacher, UpdateTeacherDisciplinesData $data): void
    {
        try {
            DB::transaction(function () use ($teacher, $data): void {
                $lockedTeacher = Teacher::query()
                    ->lockForUpdate()
                    ->findOrFail($teacher->getKey());

                $assignments = $this->normalizeAssignments($data->assignments);
                $courseIds = array_keys($assignments);
                $courses = Course::query()
                    ->whereIn('id', $courseIds)
                    ->get()
                    ->keyBy('id');

                if ($courses->count() !== count($courseIds)) {
                    throw new InvalidTeacher('Um ou mais cursos informados não foram encontrados.');
                }

                $rows = [];
                $now = now();

                foreach ($assignments as $courseId => $disciplineIds) {
                    /** @var Course $course */
                    $course = $courses->get($courseId);
                    $course->ensureIsActive();

                    $activeDisciplineIds = $course->disciplines()
                        ->whereIn('disciplines.id', $disciplineIds)
                        ->where('disciplines.is_active', true)
                        ->pluck('disciplines.id')
                        ->map(fn (mixed $id): int => (int) $id)
                        ->all();

                    if (count($activeDisciplineIds) !== count($disciplineIds)) {
                        throw new InvalidTeacher(
                            "Uma ou mais disciplinas informadas não pertencem ao curso {$course->name} ou estão inativas."
                        );
                    }

                    foreach ($activeDisciplineIds as $disciplineId) {
                        $rows[] = [
                            'teacher_id' => $lockedTeacher->id,
                            'course_id' => $course->id,
                            'discipline_id' => $disciplineId,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }

                TeacherCourseDiscipline::query()
                    ->where('teacher_id', $lockedTeacher->id)
                    ->delete();

                if ($rows !== []) {
                    TeacherCourseDiscipline::query()->insert($rows);
                }
            });
        } catch (DomainException $exception) {
            throw new InvalidTeacher($exception->getMessage(), previous: $exception);
        }
    }

    /**
     * @param  array<int|string, list<int|string>>  $assignments
     * @return array<int, list<int>>
     */
    private function normalizeAssignments(array $assignments): array
    {
        $normalized = [];

        foreach ($assignments as $courseId => $disciplineIds) {
            $courseId = (int) $courseId;
            $disciplineIds = array_values(array_unique(array_map('intval', (array) $disciplineIds)));

            if ($courseId <= 0 || $disciplineIds === []) {
                continue;
            }

            $normalized[$courseId] = $disciplineIds;
        }

        return $normalized;
    }
}
