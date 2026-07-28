<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\PeiDisciplines;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidPei;
use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidStudent;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Discipline;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Pei;
use App\Domains\SpecializedEducationalSupport\Domain\Models\PeiDiscipline;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Teacher;
use Illuminate\Database\Eloquent\Collection;

final class PeiDisciplineFormQuery
{
    /**
     * @return array<string, mixed>
     *
     * @throws InvalidPei
     * @throws InvalidStudent
     */
    public function forCreation(Pei $pei): array
    {
        $pei->loadMissing(['student', 'course.disciplines']);
        $pei->student->ensureIsActive();

        if ($pei->is_finished) {
            throw new InvalidPei('Não é possível adicionar disciplinas a um PEI finalizado.');
        }

        return [
            'pei' => $pei,
            'teachers' => $this->teachers($pei),
            'disciplines' => $pei->course->disciplines,
        ];
    }

    /**
     * @return array<string, mixed>
     *
     * @throws InvalidPei
     * @throws InvalidStudent
     */
    public function forUpdate(Pei $pei, PeiDiscipline $peiDiscipline, User $user): array
    {
        $pei->loadMissing('student');
        $pei->student->ensureIsActive();
        $peiDiscipline->loadMissing(['teacher.person', 'discipline']);
        $peiDiscipline->ensureCanBeManagedBy((int) $user->getKey());

        if ($pei->is_finished) {
            throw new InvalidPei('Não é possível editar disciplinas de um PEI finalizado.');
        }

        return [
            'pei' => $pei,
            'peiDiscipline' => $peiDiscipline,
            'teachers' => $this->teachers($pei),
            'disciplines' => Discipline::query()->orderBy('name')->get(),
        ];
    }

    /**
     * @return Collection<int, Teacher>
     */
    private function teachers(Pei $pei): Collection
    {
        return Teacher::query()
            ->whereHas('courses', function ($query) use ($pei): void {
                $query->where('courses.id', $pei->course_id);
            })
            ->with('person')
            ->orderBy('id')
            ->get();
    }
}
