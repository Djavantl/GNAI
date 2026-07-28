<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\PeiDisciplines;

use App\Domains\SpecializedEducationalSupport\Domain\Models\Pei;
use App\Models\SpecializedEducationalSupport\TeacherCourseDiscipline;
use Illuminate\Support\Collection;

final class TeacherDisciplinesQuery
{
    /**
     * @return Collection<int, array{id: int, name: string}>
     */
    public function execute(Pei $pei, int $teacherId): Collection
    {
        return TeacherCourseDiscipline::query()
            ->where('teacher_id', $teacherId)
            ->where('course_id', $pei->course_id)
            ->whereHas('discipline', function ($query): void {
                $query->where('is_active', true);
            })
            ->with('discipline')
            ->get()
            ->pluck('discipline')
            ->unique('id')
            ->sortBy('name')
            ->values()
            ->map(fn ($discipline): array => [
                'id' => (int) $discipline->id,
                'name' => (string) $discipline->name,
            ]);
    }
}
