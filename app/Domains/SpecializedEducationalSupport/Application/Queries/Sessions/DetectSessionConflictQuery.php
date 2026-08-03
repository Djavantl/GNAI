<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\Sessions;

use App\Domains\SpecializedEducationalSupport\Domain\Enums\SessionStatus;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Session;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;
use Carbon\Carbon;

final class DetectSessionConflictQuery
{
    /**
     * @param array<string, mixed> $data
     * @return array{students: array<int, string>, professional: bool, hasConflict: bool}
     */
    public function execute(array $data, ?int $ignoreId = null): array
    {
        $date = $data['session_date'];
        $start = Carbon::parse($data['start_time'])->format('H:i:00');
        $end = Carbon::parse($data['end_time'])->format('H:i:00');

        $baseQuery = Session::query()
            ->whereDate('session_date', $date)
            ->where('status', '!=', SessionStatus::CANCELLED_DATABASE_VALUE)
            ->when($ignoreId !== null, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->where(function ($query) use ($start, $end): void {
                $query->whereTime('start_time', '<', $end)
                    ->whereTime('end_time', '>', $start);
            });

        $professionalConflict = (clone $baseQuery)
            ->where('professional_id', $data['professional_id'])
            ->exists();

        $studentIds = $data['student_ids'] ?? [];

        $conflictingStudents = (clone $baseQuery)
            ->whereHas('students', fn ($query) => $query->whereIn('students.id', $studentIds))
            ->with('students.person')
            ->get()
            ->pluck('students')
            ->flatten()
            ->filter(fn (Student $student): bool => in_array($student->id, $studentIds, true))
            ->mapWithKeys(fn (Student $student): array => [$student->id => $student->person->name])
            ->toArray();

        return [
            'students' => $conflictingStudents,
            'professional' => $professionalConflict,
            'hasConflict' => $professionalConflict || $conflictingStudents !== [],
        ];
    }
}
