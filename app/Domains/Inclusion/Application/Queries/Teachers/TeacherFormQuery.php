<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Queries\Teachers;

use App\Domains\Inclusion\Domain\Models\Discipline;
use App\Domains\Inclusion\Domain\Models\Teacher;
use Illuminate\Database\Eloquent\Collection;

final class TeacherFormQuery
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
    public function forUpdate(Teacher $teacher): array
    {
        $teacher->loadMissing(['person', 'disciplines']);
        $teacher->person?->append('photo_url');

        return [
            'teacher' => $teacher,
            'disciplines' => $this->activeDisciplines(),
            'selectedDisciplines' => $teacher->disciplines->pluck('id')->toArray(),
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
