<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\UI\Presenters;

use App\Domains\Inclusion\Application\Data\StudentContexts\ListStudentContextsData;
use App\Domains\Inclusion\Domain\Enums\ContextEvaluationType;
use App\Domains\Inclusion\Domain\Models\Student;
use App\Domains\Inclusion\Domain\Models\StudentContext;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

final class StudentContextPagePresenter
{
    /**
     * @param  LengthAwarePaginator<int, StudentContext>  $contexts
     * @param  array<string, mixed>  $filterOptions
     * @return array<string, mixed>
     */
    public function index(
        LengthAwarePaginator $contexts,
        Student $student,
        ListStudentContextsData $filters,
        array $filterOptions,
        Request $request,
    ): array {
        $user = $request->user();

        return [
            'contexts' => $contexts->through(fn (StudentContext $context): array => [
                ...$context->toArray(),
                'evaluation_type_label' => $this->evaluationTypeLabel($context),
                'can_manage' => $context->canBeManagedByCurrentUser(),
            ]),
            'student' => $student,
            // A criação depende de todo o histórico, não da página filtrada.
            'hasContexts' => $student->contexts()->exists(),
            'semesters' => collect($filterOptions['semesters'])
                ->except('')
                ->map(static fn (mixed $label, int|string $id): array => ['value' => (string) $id, 'label' => (string) $label])
                ->values(),
            'filters' => [
                'semester_id' => $filters->semesterId === null ? '' : (string) $filters->semesterId,
                'evaluation_type' => $filters->evaluationType?->value ?? '',
                'is_current' => $filters->isCurrent === null ? '' : (string) (int) $filters->isCurrent,
            ],
            'can' => [
                'create' => $user?->can('student-context.create') ?? false,
                'view' => $user?->can('student-context.view') ?? false,
                'delete' => $user?->can('student-context.delete') ?? false,
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $display
     * @return array<string, mixed>
     */
    public function show(StudentContext $studentContext, array $display, Request $request): array
    {
        $student = $studentContext->student;
        $user = $request->user();
        $canUpdate = $user?->can('student-context.update') ?? false;
        $canManage = $studentContext->canBeManagedByCurrentUser();

        return [
            'studentContext' => $studentContext,
            'student' => $student,
            'deficiencies' => $student->deficiencies,
            ...$display,
            'evaluationTypeLabel' => $this->evaluationTypeLabel($studentContext),
            'can' => [
                'update' => $studentContext->is_current && $canUpdate && $canManage,
                'restore' => ! $studentContext->is_current && $canUpdate,
                'delete' => $canManage && ($user?->can('student-context.delete') ?? false),
            ],
        ];
    }

    private function evaluationTypeLabel(StudentContext $context): string
    {
        return ContextEvaluationType::tryFrom((string) $context->evaluation_type)?->label()
            ?? (string) $context->evaluation_type;
    }
}
