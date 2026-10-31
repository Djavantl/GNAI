<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\UI\Controllers;

use App\Domains\SpecializedEducationalSupport\Application\Actions\StudentContexts\CreateStudentContextAction;
use App\Domains\SpecializedEducationalSupport\Application\Actions\StudentContexts\CreateStudentContextVersionAction;
use App\Domains\SpecializedEducationalSupport\Application\Actions\StudentContexts\DeleteStudentContextAction;
use App\Domains\SpecializedEducationalSupport\Application\Actions\StudentContexts\RestoreStudentContextVersionAction;
use App\Domains\SpecializedEducationalSupport\Application\Actions\StudentContexts\UpdateStudentContextAction;
use App\Domains\SpecializedEducationalSupport\Application\Data\StudentContexts\CreateStudentContextData;
use App\Domains\SpecializedEducationalSupport\Application\Data\StudentContexts\ListStudentContextsData;
use App\Domains\SpecializedEducationalSupport\Application\Data\StudentContexts\UpdateStudentContextData;
use App\Domains\SpecializedEducationalSupport\Application\Queries\StudentContexts\CurrentStudentContextQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\StudentContexts\ListStudentContextsQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\StudentContexts\ShowStudentContextQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\StudentContexts\StudentContextFilterOptionsQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\StudentContexts\StudentContextFormQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\StudentContexts\StudentContextPdfQuery;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\ContextEvaluationType;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;
use App\Domains\SpecializedEducationalSupport\Domain\Models\StudentContext;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Throwable;

final class StudentContextController
{
    public function index(ListStudentContextsData $filters, Student $student, ListStudentContextsQuery $query, StudentContextFilterOptionsQuery $filterOptions, Request $request): InertiaResponse
    {
        $contexts = $query->execute($student, $filters)->through(
            static fn (StudentContext $context): array => [
                ...$context->toArray(),
                'evaluation_type_label' => ContextEvaluationType::tryFrom((string) $context->evaluation_type)?->label()
                    ?? (string) $context->evaluation_type,
                'can_manage' => $context->canBeManagedByCurrentUser(),
            ],
        );
        $student->loadMissing('person');
        $user = $request->user();

        return Inertia::render('SpecializedEducationalSupport/StudentContext/Index', [
            'contexts' => $contexts,
            'student' => $student,
            // The create action depends on the full history, not on the filtered page.
            'hasContexts' => $student->contexts()->exists(),
            'semesters' => collect($filterOptions->execute()['semesters'])
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
        ]);
    }

    public function show(StudentContext $studentContext, ShowStudentContextQuery $query, StudentContextFormQuery $form, Request $request): InertiaResponse
    {
        return $this->renderShow($query->execute($studentContext), $form, $request);
    }

    public function showCurrent(Student $student, CurrentStudentContextQuery $query, StudentContextFormQuery $form, Request $request): InertiaResponse
    {
        return $this->renderShow($query->execute($student), $form, $request);
    }

    public function create(Student $student, StudentContextFormQuery $form): InertiaResponse
    {
        return Inertia::render(
            'SpecializedEducationalSupport/StudentContext/Create',
            $form->forCreation($student),
        );
    }

    /**
     * @throws Throwable
     */
    public function store(CreateStudentContextData $data, Student $student, CreateStudentContextAction $action, Request $request): RedirectResponse
    {
        $action->execute($student, $data, $this->professionalId($request));

        return redirect()
            ->route('specialized-educational-support.students.show', [
                'student' => $student,
                'tab' => 'current-context',
            ])
            ->with('success', 'Contexto do aluno cadastrado com sucesso.');
    }

    public function edit(StudentContext $studentContext, StudentContextFormQuery $form, Request $request): InertiaResponse
    {
        return Inertia::render(
            'SpecializedEducationalSupport/StudentContext/Edit',
            $form->forUpdate($studentContext, $this->professionalId($request)),
        );
    }

    /**
     * @throws Throwable
     */
    public function update(UpdateStudentContextData $data, StudentContext $studentContext, UpdateStudentContextAction $action, Request $request): RedirectResponse
    {
        $studentContext = $action->execute(
            $studentContext,
            $data,
            $this->professionalId($request),
        );

        return redirect()
            ->route('specialized-educational-support.students.show', [
                'student' => $studentContext->student_id,
                'tab' => 'current-context',
            ])
            ->with('success', 'Contexto salvo com sucesso.');
    }

    public function makeNewVersion(Student $student, StudentContextFormQuery $form): InertiaResponse
    {
        return Inertia::render(
            'SpecializedEducationalSupport/StudentContext/Version',
            $form->forNewVersion($student),
        );
    }

    /**
     * @throws Throwable
     */
    public function storeNewVersion(UpdateStudentContextData $data, Student $student, CreateStudentContextVersionAction $action, Request $request): RedirectResponse
    {
        $newContext = $action->execute(
            $student,
            $data,
            $this->professionalId($request),
        );

        return redirect()
            ->route('specialized-educational-support.students.show', [
                'student' => $newContext->student_id,
                'tab' => 'current-context',
            ])
            ->with('success', 'Nova versão criada e definida como atual.');
    }

    /**
     * @throws Throwable
     */
    public function restoreVersion(StudentContext $studentContext, RestoreStudentContextVersionAction $action, Request $request): RedirectResponse
    {
        $newContext = $action->execute(
            $studentContext,
            $this->professionalId($request),
        );

        return redirect()
            ->route('specialized-educational-support.students.show', [
                'student' => $newContext->student_id,
                'tab' => 'current-context',
            ])
            ->with('success', 'Contexto restaurado e definido como atual.');
    }

    /**
     * @throws Throwable
     */
    public function destroy(StudentContext $studentContext, DeleteStudentContextAction $action, Request $request): RedirectResponse
    {
        $student = $action->execute(
            $studentContext,
            $this->professionalId($request),
        );

        return redirect()
            ->route('specialized-educational-support.students.show', [
                'student' => $student,
                'tab' => 'current-context',
            ])
            ->with('success', 'Contexto do aluno removido com sucesso.');
    }

    public function generatePdf(StudentContext $studentContext, StudentContextPdfQuery $query): Response
    {
        $context = $query->execute($studentContext);
        $student = $context->student;

        return Pdf::loadView(
            'pages.specialized-educational-support.student-context.pdf',
            compact('context', 'student'),
        )
            ->setPaper('a4', 'portrait')
            ->setOption(['enable_php' => true])
            ->stream("Contexto_{$student->person->name}.pdf");
    }

    private function renderShow(StudentContext $studentContext, StudentContextFormQuery $form, Request $request): InertiaResponse
    {
        $student = $studentContext->student;
        $user = $request->user();
        $canUpdate = $user?->can('student-context.update') ?? false;
        $canManage = $studentContext->canBeManagedByCurrentUser();

        return Inertia::render('SpecializedEducationalSupport/StudentContext/Show', [
            'studentContext' => $studentContext,
            'student' => $student,
            'deficiencies' => $student->deficiencies,
            ...$form->forDisplay($student),
            'evaluationTypeLabel' => ContextEvaluationType::tryFrom((string) $studentContext->evaluation_type)?->label()
                ?? (string) $studentContext->evaluation_type,
            // Same conditions the legacy Blade evaluated for each action.
            'can' => [
                'update' => $studentContext->is_current && $canUpdate && $canManage,
                'restore' => ! $studentContext->is_current && $canUpdate,
                'delete' => $canManage && ($user?->can('student-context.delete') ?? false),
            ],
        ]);
    }

    private function professionalId(Request $request): ?int
    {
        $professionalId = $request->user()?->professional_id;

        return $professionalId === null ? null : (int) $professionalId;
    }
}
