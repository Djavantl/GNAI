<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\UI\Presenters;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\Inclusion\Domain\Models\PedagogicalRecord;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

final class PedagogicalRecordPagePresenter
{
    /**
     * @param  LengthAwarePaginator<int, PedagogicalRecord>  $pedagogicalRecords
     * @param  array<string, mixed>  $filterOptions
     * @return array<string, mixed>
     */
    public function listing(LengthAwarePaginator $pedagogicalRecords, array $filterOptions, User $user, Request $request): array
    {
        $professionalId = $user->professional_id;
        $pedagogicalRecords->through(static function (PedagogicalRecord $record) use ($professionalId): array {
            $session = $record->attendanceSession;
            $student = $session?->students?->first();

            return [
                'id' => $record->getKey(),
                'session_date' => $session?->session_date?->toDateString(),
                'start_time' => $session?->start_time,
                'end_time' => $session?->end_time,
                'duration' => $record->duration,
                'is_present' => (bool) $record->is_present,
                'has_guardians' => $record->guardians->isNotEmpty(),
                'student_name' => $student?->person?->name,
                'course_name' => $student?->currentCourse?->course?->name,
                'professional_name' => $session?->professional?->person?->name,
                'can_manage' => $professionalId !== null
                    && (int) $session?->professional_id === (int) $professionalId,
            ];
        });
        $personOption = static fn ($model): array => [
            'id' => $model->getKey(),
            'name' => $model->person->name ?? ('ID '.$model->getKey()),
        ];

        return [
            'pedagogicalRecords' => $pedagogicalRecords,
            'students' => $filterOptions['students']->map($personOption)->values(),
            'professionals' => $filterOptions['professionals']->map($personOption)->values(),
            'courses' => $filterOptions['courses']->map(static fn ($course): array => [
                'id' => $course->getKey(),
                'name' => $course->name,
            ])->values(),
            'filters' => collect(['student', 'professional_id', 'course_id', 'is_present', 'with_guardians'])
                ->mapWithKeys(fn (string $key): array => [$key => $request->string($key)->toString()])
                ->all(),
            'can' => [
                'view' => $user->can('pedagogical-record.view'),
                'view_all' => $user->can('pedagogical-record.view-all'),
                'view_own' => $user->can('pedagogical-record.view-own'),
                'update' => $user->can('pedagogical-record.update'),
                'delete' => $user->can('pedagogical-record.delete'),
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function show(PedagogicalRecord $pedagogicalRecord, User $user): array
    {
        $session = $pedagogicalRecord->attendanceSession;
        $student = $session->students->first();
        $canManage = $user->professional_id !== null
            && (int) $session->professional_id === (int) $user->professional_id;

        return [
            'pedagogicalRecord' => [
                'id' => $pedagogicalRecord->getKey(),
                'follow_up_reason' => $pedagogicalRecord->follow_up_reason,
                'duration' => $pedagogicalRecord->duration,
                'is_present' => (bool) $pedagogicalRecord->is_present,
                'absence_reason' => $pedagogicalRecord->absence_reason,
                'systematic_pedagogical_follow_up_record' => $pedagogicalRecord->systematic_pedagogical_follow_up_record,
                'strategies_and_resources_adopted' => $pedagogicalRecord->strategies_and_resources_adopted,
                'referrals_made' => $pedagogicalRecord->referrals_made,
                'complementary_observations' => $pedagogicalRecord->complementary_observations,
                'has_guardians' => $pedagogicalRecord->guardians->isNotEmpty(),
                'guardian_names' => $pedagogicalRecord->guardian_names,
            ],
            'session' => [
                'id' => (int) $session->id,
                'session_date' => $session->session_date?->format('d/m/Y'),
                'start_time' => $session->start_time ? Carbon::parse($session->start_time)->format('H:i') : null,
                'end_time' => $session->end_time ? Carbon::parse($session->end_time)->format('H:i') : null,
                'student_name' => $student?->person?->name,
                'professional_name' => $session->professional?->person?->name,
            ],
            'can' => [
                'update' => $canManage && $user->can('pedagogical-record.update'),
                'delete' => $canManage && $user->can('pedagogical-record.delete'),
                'pdf' => $user->can('pedagogical-record.view'),
            ],
        ];
    }
}
