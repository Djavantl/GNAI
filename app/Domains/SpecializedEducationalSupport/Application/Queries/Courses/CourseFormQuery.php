<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\Courses;

use App\Domains\SpecializedEducationalSupport\Domain\Models\Course;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Discipline;
use Illuminate\Database\Eloquent\Collection;

final class CourseFormQuery
{
    /**
     * @return array<string, mixed>
     */
    public function forCreation(): array
    {
        return [
            'disciplines' => $this->activeDisciplines(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function forUpdate(Course $course): array
    {
        $course->loadMissing('disciplines');

        return [
            'course' => $course,
            'disciplines' => $this->activeDisciplines(),
        ];
    }

    /**
     * @return Collection<int, Discipline>
     */
    private function activeDisciplines(): Collection
    {
        return Discipline::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }
}
