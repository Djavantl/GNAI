<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\UI\Presenters;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\Inclusion\Domain\Models\AeeStudentEvaluation;
use App\Domains\Inclusion\Domain\Models\Student;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

final class AeeRecordPagePresenter
{
    /**
     * @param  array<string, mixed>  $filterOptions
     * @param  list<string>  $filterKeys
     * @return array<string, mixed>
     */
    public function listing(array $filterOptions, User $user, Request $request, array $filterKeys): array
    {
        return $filterOptions + [
            'filters' => collect($filterKeys)
                ->mapWithKeys(fn (string $key): array => [$key => (string) $request->query($key, '')])
                ->all(),
            'can' => [
                'view' => $user->can('aee-record.view'),
                'view_all' => $user->can('aee-record.view-all'),
                'view_own' => $user->can('aee-record.view-own'),
            ],
        ];
    }

    /**
     * @param  LengthAwarePaginator<int, AeeStudentEvaluation>  $aeeEvaluations
     * @param  array<string, mixed>  $filterOptions
     * @return array<string, mixed>
     */
    public function studentIndex(Student $student, LengthAwarePaginator $aeeEvaluations, array $filterOptions, User $user, Request $request): array
    {
        $professionalId = $user->professional_id;
        $aeeEvaluations->getCollection()->each(static function (AeeStudentEvaluation $evaluation) use ($professionalId): void {
            $evaluation->setAttribute(
                'can_manage',
                $professionalId !== null
                    && (int) $evaluation->aeeRecord?->attendanceSession?->professional_id === (int) $professionalId,
            );
        });

        return [
            'can' => [
                'view' => $user->can('aee-record.view'),
                'update' => $user->can('aee-record.update'),
                'delete' => $user->can('aee-record.delete'),
                'view_all' => $user->can('aee-record.view-all'),
            ],
            'student' => $student,
            'aeeEvaluations' => $aeeEvaluations,
        ] + $this->listing($filterOptions, $user, $request, ['professional_id', 'is_present']);
    }

    /**
     * Permissão da ação e autoria do profissional vinculado ao agendamento, como no legado.
     *
     * @return array{update: bool, delete: bool}
     */
    public function managementAbilities(User $user, int $sessionProfessionalId): array
    {
        $isOwner = $user->professional_id !== null && (int) $user->professional_id === $sessionProfessionalId;

        return [
            'update' => $isOwner && $user->can('aee-record.update'),
            'delete' => $isOwner && $user->can('aee-record.delete'),
        ];
    }
}
