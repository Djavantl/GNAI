<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\Teachers;

use App\Models\SpecializedEducationalSupport\Course;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Teacher;
use Illuminate\Database\Eloquent\Collection;

final class TeacherDisciplinesFormQuery
{
    /**
     * @return array<string, mixed>
     */
    public function execute(Teacher $teacher): array
    {
        $teacher->load(['person', 'courses', 'courseDisciplines.discipline']);

        $selectedByCourse = $teacher->courseDisciplines
            ->groupBy('course_id')
            ->map(fn ($items) => $items->pluck('discipline_id')->toArray())
            ->toArray();

        return [
            'teacher' => $teacher,
            'courses' => $this->activeCoursesWithDisciplines(),
            'selectedByCourse' => $selectedByCourse,
        ];
    }

    /**
     * @return Collection<int, Course>
     */
    private function activeCoursesWithDisciplines(): Collection
    {
        return Course::query()
            ->with(['disciplines' => function ($query): void {
                $query->where('is_active', true)->orderBy('name');
            }])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }
}
