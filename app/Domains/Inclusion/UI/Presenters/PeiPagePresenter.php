<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\UI\Presenters;

use App\Domains\Inclusion\Application\Data\Peis\ListPeisData;
use App\Domains\Inclusion\Domain\Enums\AttentionLevel;
use App\Domains\Inclusion\Domain\Enums\AutonomyLevel;
use App\Domains\Inclusion\Domain\Enums\CommunicationType;
use App\Domains\Inclusion\Domain\Enums\ContextEvaluationType;
use App\Domains\Inclusion\Domain\Enums\GeneralLevel;
use App\Domains\Inclusion\Domain\Enums\InteractionLevel;
use App\Domains\Inclusion\Domain\Enums\MemoryLevel;
use App\Domains\Inclusion\Domain\Enums\ReasoningLevel;
use App\Domains\Inclusion\Domain\Enums\SocializationLevel;
use App\Domains\Inclusion\Domain\Models\Discipline;
use App\Domains\Inclusion\Domain\Models\Pei;
use App\Domains\Inclusion\Domain\Models\PeiDiscipline;
use App\Domains\Inclusion\Domain\Models\Semester;
use App\Domains\Inclusion\Domain\Models\Student;
use App\Domains\Inclusion\Domain\Models\StudentContext;
use App\Shared\Infrastructure\Security\RichTextSanitizer;
use BackedEnum;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

final class PeiPagePresenter
{
    /**
     * @param  LengthAwarePaginator<int, Pei>  $peis
     * @param  Collection<int, Student>  $students
     * @param  Collection<int, Semester>  $semesters
     * @return array<string, mixed>
     */
    public function all(
        LengthAwarePaginator $peis,
        Collection $students,
        Collection $semesters,
        ListPeisData $filters,
        Request $request,
    ): array {
        return [
            'peis' => $peis,
            'students' => $students
                ->map(static fn (Student $student): array => [
                    'value' => (string) $student->getKey(),
                    'label' => (string) ($student->person?->name ?? ''),
                ])
                ->values(),
            'semesters' => $this->semesterOptions($semesters),
            'filters' => $this->listFilterValues($filters) + [
                'student_id' => $filters->studentId === null ? '' : (string) $filters->studentId,
            ],
            'can' => $this->listPermissions($request),
        ];
    }

    /**
     * @param  LengthAwarePaginator<int, Pei>  $peis
     * @param  Collection<int, Semester>  $semesters
     * @param  Collection<int, Discipline>  $disciplines
     * @return array<string, mixed>
     */
    public function index(
        LengthAwarePaginator $peis,
        Student $student,
        Collection $semesters,
        Collection $disciplines,
        ListPeisData $filters,
        Request $request,
    ): array {
        return [
            'peis' => $peis,
            'student' => [
                'id' => $student->getKey(),
                'name' => (string) ($student->person?->name ?? ''),
            ],
            'semesters' => $this->semesterOptions($semesters),
            'disciplines' => $disciplines,
            'filters' => $this->listFilterValues($filters),
            'can' => $this->listPermissions($request),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function show(Pei $pei, Request $request): array
    {
        $user = $request->user();
        $userId = $user?->getKey() === null ? null : (int) $user->getKey();
        $canViewContext = $user?->can('student-context.view') ?? false;
        $canViewDisciplines = $user?->can('pei-discipline.view') ?? false;
        $canUpdateDiscipline = $user?->can('pei-discipline.update') ?? false;
        $canDeleteDiscipline = $user?->can('pei-discipline.delete') ?? false;
        $isCreator = $userId !== null && (int) $pei->creator_id === $userId;

        $pei->loadMissing([
            'student.person',
            'student.deficiencies',
            'course',
            'semester',
            'creator',
        ]);

        $student = $pei->student;
        $creator = $pei->creator;
        $tab = $request->string('tab')->toString();

        $disciplines = null;

        if ($canViewDisciplines) {
            $disciplines = $pei->peiDisciplines()
                ->with(['discipline', 'teacher.person'])
                ->latest()
                ->get()
                ->map(function (PeiDiscipline $item) use ($pei, $userId, $canUpdateDiscipline, $canDeleteDiscipline): array {
                    $manageable = ! $pei->is_finished
                        && $userId !== null
                        && (int) $item->creator_id === $userId;

                    return [
                        'id' => (int) $item->getKey(),
                        'discipline_name' => $item->discipline?->name,
                        'teacher_name' => $item->teacher?->person?->name,
                        'created_at' => $item->created_at?->toDateString(),
                        'can_update' => $manageable && $canUpdateDiscipline,
                        'can_delete' => $manageable && $canDeleteDiscipline,
                    ];
                })
                ->values()
                ->all();
        }

        return [
            'pei' => [
                'id' => (int) $pei->getKey(),
                'version' => $pei->version,
                'is_finished' => (bool) $pei->is_finished,
                'created_at' => $pei->created_at?->toDateString(),
                'course_name' => $pei->course?->name,
                'semester_label' => $pei->semester?->label,
                'responsible' => $creator?->is_admin
                    ? 'admin'
                    : ($creator?->name ?: 'Sistema/Desconhecido'),
            ],
            'student' => [
                'id' => (int) $student->getKey(),
                'name' => (string) ($student->person?->name ?? ''),
                'registration' => $student->registration,
                'photo_url' => $student->person?->photo_url,
                'status_label' => $student->status?->label(),
                'status_color' => $student->status?->color(),
                'deficiencies' => $student->deficiencies
                    ->pluck('name')
                    ->filter()
                    ->values()
                    ->all(),
            ],
            'studentContext' => $canViewContext ? $this->context($pei) : null,
            'peiDisciplines' => $disciplines,
            'activeTab' => $tab,
            'can' => [
                'viewContext' => $canViewContext,
                'viewDisciplines' => $canViewDisciplines,
                // As ações de domínio também exigem o criador do PEI e um PEI em aberto.
                'createDiscipline' => ! $pei->is_finished
                    && ($user?->can('pei-discipline.create') ?? false),
                'finish' => ! $pei->is_finished
                    && $isCreator
                    && ($user?->can('pei.update') ?? false),
                'delete' => $isCreator && ($user?->can('pei.delete') ?? false),
                'pdf' => $user?->can('pei.view') ?? false,
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function discipline(Pei $pei, PeiDiscipline $peiDiscipline, Request $request): array
    {
        $user = $request->user();
        $userId = $user?->getKey() === null ? null : (int) $user->getKey();
        $manageable = ! $pei->is_finished
            && $userId !== null
            && (int) $peiDiscipline->creator_id === $userId;

        $pei->loadMissing('student.person');
        $peiDiscipline->loadMissing(['discipline', 'teacher.person']);

        return [
            'pei' => [
                'id' => (int) $pei->getKey(),
                'version' => $pei->version,
                'is_finished' => (bool) $pei->is_finished,
            ],
            'student' => [
                'id' => (int) $pei->student->getKey(),
                'name' => (string) ($pei->student->person?->name ?? ''),
            ],
            'peiDiscipline' => [
                'id' => (int) $peiDiscipline->getKey(),
                'discipline_name' => $peiDiscipline->discipline?->name,
                'teacher_name' => $peiDiscipline->teacher?->person?->name,
                'specific_objectives_html' => $this->richText($peiDiscipline->specific_objectives),
                'content_programmatic_html' => $this->richText($peiDiscipline->content_programmatic),
                'methodologies_html' => $this->richText($peiDiscipline->methodologies),
                'evaluations_html' => $this->richText($peiDiscipline->evaluations),
                'complementary_records_html' => $this->richText(
                    $peiDiscipline->complementary_records,
                    'Nenhum registro complementar informado.',
                ),
                'opinion_html' => $this->richText($peiDiscipline->opinion),
            ],
            'can' => [
                'update' => $manageable && ($user?->can('pei-discipline.update') ?? false),
                'delete' => $manageable && ($user?->can('pei-discipline.delete') ?? false),
                'pdf' => $user?->can('pei.view') ?? false,
            ],
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function context(Pei $pei): ?array
    {
        $pei->loadMissing(['studentContext.semester', 'studentContext.evaluator.person']);
        $context = $pei->studentContext;

        if (! $context instanceof StudentContext) {
            return null;
        }

        return [
            'id' => (int) $context->getKey(),
            'semester_label' => $context->semester?->label ?? $context->semester?->name,
            'evaluation_type_label' => $this->enumLabel(ContextEvaluationType::class, $context->evaluation_type),
            'version' => $context->version,
            'is_current' => (bool) $context->is_current,
            'history_html' => $this->richText($context->history),
            'specific_educational_needs_html' => $this->richText($context->specific_educational_needs),
            'knowledge_html' => $this->richText($context->knowledge),
            'difficulties_html' => $this->richText($context->difficulties),
            'learning_level_label' => $this->enumLabel(GeneralLevel::class, $context->learning_level),
            'attention_level_label' => $this->enumLabel(AttentionLevel::class, $context->attention_level),
            'memory_level_label' => $this->enumLabel(MemoryLevel::class, $context->memory_level),
            'reasoning_level_label' => $this->enumLabel(ReasoningLevel::class, $context->reasoning_level),
            'learning_observations_html' => $this->richText($context->learning_observations),
            'communication_type_label' => $this->enumLabel(CommunicationType::class, $context->communication_type),
            'interaction_level_label' => $this->enumLabel(InteractionLevel::class, $context->interaction_level),
            'socialization_level_label' => $this->enumLabel(SocializationLevel::class, $context->socialization_level),
            'shows_aggressive_behavior' => (bool) $context->shows_aggressive_behavior,
            'shows_withdrawn_behavior' => (bool) $context->shows_withdrawn_behavior,
            'behavior_notes_html' => $this->richText($context->behavior_notes),
            'autonomy_level_label' => $this->enumLabel(AutonomyLevel::class, $context->autonomy_level),
            'needs_mobility_support_html' => $this->richText($context->needs_mobility_support),
            'needs_communication_support_html' => $this->richText($context->needs_communication_support),
            'needs_pedagogical_adaptation_html' => $this->richText($context->needs_pedagogical_adaptation),
            'uses_assistive_technology_html' => $this->richText($context->uses_assistive_technology),
            'has_medical_report' => (bool) $context->has_medical_report,
            'uses_medication' => (bool) $context->uses_medication,
            'medical_notes_html' => $this->richText($context->medical_notes),
            'evaluator_name' => $context->evaluator?->person?->name,
            'created_at' => $context->created_at?->toIso8601String(),
            'updated_at' => $context->updated_at?->toIso8601String(),
        ];
    }

    /**
     * @return array{semester_id: string, version: string, is_finished: string}
     */
    private function listFilterValues(ListPeisData $filters): array
    {
        $isFinished = $filters->isFinished;

        return [
            'semester_id' => $filters->semesterId === null ? '' : (string) $filters->semesterId,
            'version' => $filters->version === null ? '' : (string) $filters->version,
            'is_finished' => $isFinished === null || $isFinished === ''
                ? ''
                : (string) (int) filter_var($isFinished, FILTER_VALIDATE_BOOLEAN),
        ];
    }

    /**
     * @param  Collection<int, Semester>  $semesters
     * @return list<array{value: string, label: string}>
     */
    private function semesterOptions(Collection $semesters): array
    {
        return $semesters
            ->map(static fn (Semester $semester): array => [
                'value' => (string) $semester->getKey(),
                'label' => (string) $semester->label,
            ])
            ->values()
            ->all();
    }

    /**
     * @return array{create: bool, view: bool, delete: bool}
     */
    private function listPermissions(Request $request): array
    {
        $user = $request->user();

        return [
            'create' => $user?->can('pei.create') ?? false,
            'view' => $user?->can('pei.view') ?? false,
            'delete' => $user?->can('pei.delete') ?? false,
        ];
    }

    /**
     * @param  class-string<BackedEnum>  $enum
     */
    private function enumLabel(string $enum, mixed $value): ?string
    {
        if ($value instanceof BackedEnum) {
            $value = $value->value;
        }

        if ($value === null || $value === '') {
            return null;
        }

        $case = $enum::tryFrom((string) $value);

        return $case !== null && method_exists($case, 'label')
            ? $case->label()
            : (string) $value;
    }

    /** Texto simples mantém as quebras de linha; HTML é sanitizado como nas caixas do legado. */
    private function richText(?string $value, string $empty = '---'): string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return e($empty);
        }

        if ($value === strip_tags($value)) {
            return nl2br(e($value), false);
        }

        return RichTextSanitizer::sanitize($value);
    }
}
