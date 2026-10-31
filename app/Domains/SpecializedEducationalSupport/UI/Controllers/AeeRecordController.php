<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\UI\Controllers;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\SpecializedEducationalSupport\Application\Actions\AeeRecords\CreateAeeRecordAction;
use App\Domains\SpecializedEducationalSupport\Application\Actions\AeeRecords\DeleteAeeRecordAction;
use App\Domains\SpecializedEducationalSupport\Application\Actions\AeeRecords\DeleteAeeStudentEvaluationAction;
use App\Domains\SpecializedEducationalSupport\Application\Actions\AeeRecords\UpdateAeeRecordAction;
use App\Domains\SpecializedEducationalSupport\Application\Actions\AeeRecords\UpdateAeeStudentEvaluationAction;
use App\Domains\SpecializedEducationalSupport\Application\Data\AeeRecords\CreateAeeRecordData;
use App\Domains\SpecializedEducationalSupport\Application\Data\AeeRecords\ListAeeRecordsData;
use App\Domains\SpecializedEducationalSupport\Application\Data\AeeRecords\UpdateAeeRecordData;
use App\Domains\SpecializedEducationalSupport\Application\Data\AeeRecords\UpdateAeeStudentEvaluationData;
use App\Domains\SpecializedEducationalSupport\Application\Queries\AeeRecords\AeeRecordFormQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\AeeRecords\ListAeeRecordsQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\AeeRecords\ListStudentAeeEvaluationsQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\AeeRecords\ShowAeeRecordQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\AttendanceRecords\AttendanceRecordFilterOptionsQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\Students\StudentAeeEvaluationsQuery;
use App\Domains\SpecializedEducationalSupport\Domain\Models\AeeRecord;
use App\Domains\SpecializedEducationalSupport\Domain\Models\AeeStudentEvaluation;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Session;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;
use App\Shared\Application\Exceptions\AccessDeniedException;
use App\Shared\Infrastructure\Pdf\PdfPageNumberer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Throwable;

final class AeeRecordController
{
    public function index(ListAeeRecordsData $filters, ListAeeRecordsQuery $query, AttendanceRecordFilterOptionsQuery $options, Request $request): InertiaResponse
    {
        $aeeRecords = $query->execute($filters, $this->user($request));

        return Inertia::render(
            'SpecializedEducationalSupport/AeeRecords/Index',
            compact('aeeRecords') + $options->execute() + $this->listProps($request, ['student', 'professional_id', 'course_id', 'is_present']),
        );
    }

    public function myRecords(ListAeeRecordsData $filters, ListAeeRecordsQuery $query, AttendanceRecordFilterOptionsQuery $options, Request $request): InertiaResponse
    {
        $aeeRecords = $query->execute($filters, $this->user($request), onlyOwn: true);

        return Inertia::render(
            'SpecializedEducationalSupport/AeeRecords/MyRecords',
            compact('aeeRecords') + $options->execute() + $this->listProps($request, ['student', 'course_id', 'is_present']),
        );
    }

    public function create(Session $session, AeeRecordFormQuery $form, Request $request): InertiaResponse
    {
        return Inertia::render('SpecializedEducationalSupport/AeeRecords/Create', $form->forCreation($session, $this->professionalId($request)));
    }

    /** @throws Throwable */
    public function store(CreateAeeRecordData $data, CreateAeeRecordAction $action, Request $request): RedirectResponse
    {
        $aeeRecord = $action->execute($data, $this->professionalId($request));

        return redirect()->route('specialized-educational-support.aee-records.show', $aeeRecord)->with('success', 'Registro do atendimento AEE criado com sucesso.');
    }

    public function show(AeeRecord $aeeRecord, ShowAeeRecordQuery $query, Request $request): InertiaResponse
    {
        $aeeRecord = $query->execute($aeeRecord, $this->user($request));
        $session = $aeeRecord->attendanceSession;
        $can = $this->managementAbilities($request, (int) $session->professional_id);

        return Inertia::render('SpecializedEducationalSupport/AeeRecords/Show', compact('aeeRecord', 'session', 'can'));
    }

    public function edit(AeeRecord $aeeRecord, AeeRecordFormQuery $form, Request $request): InertiaResponse
    {
        return Inertia::render('SpecializedEducationalSupport/AeeRecords/Edit', $form->forUpdate($aeeRecord, $this->professionalId($request)));
    }

    /** @throws Throwable */
    public function update(UpdateAeeRecordData $data, AeeRecord $aeeRecord, UpdateAeeRecordAction $action, Request $request): RedirectResponse
    {
        $aeeRecord = $action->execute($aeeRecord, $data, $this->professionalId($request));

        return redirect()->route('specialized-educational-support.aee-records.show', $aeeRecord)->with('success', 'Registro do atendimento AEE atualizado com sucesso.');
    }

    /** @throws Throwable */
    public function destroy(AeeRecord $aeeRecord, DeleteAeeRecordAction $action, Request $request): RedirectResponse
    {
        $sessionId = $action->execute($aeeRecord, $this->professionalId($request));

        return redirect()->route('specialized-educational-support.sessions.show', $sessionId)->with('success', 'Registro do atendimento AEE removido com sucesso.');
    }

    public function pdf(AeeRecord $aeeRecord, ShowAeeRecordQuery $query, Request $request): Response
    {
        $aeeRecord = $query->execute($aeeRecord, $this->user($request));
        $session = $aeeRecord->attendanceSession;
        $professional = $session->professional;
        $evaluations = $aeeRecord->studentEvaluations;
        $pdf = Pdf::loadView('pages.specialized-educational-support.aee-records.pdf', compact('aeeRecord', 'session', 'professional', 'evaluations'))->setPaper('a4', 'portrait');
        PdfPageNumberer::apply($pdf);

        return $pdf->stream("registro-aee-{$session->session_date->format('d-m-Y')}-{$aeeRecord->id}.pdf");
    }

    public function studentIndex(Student $student, ListAeeRecordsData $filters, ListStudentAeeEvaluationsQuery $query, AttendanceRecordFilterOptionsQuery $options, Request $request): InertiaResponse
    {
        $user = $this->user($request);
        $aeeEvaluations = $query->execute($student, $filters, $user);
        $student->loadMissing('person');
        $professionalId = $user->professional_id;
        $aeeEvaluations->getCollection()->each(static function (AeeStudentEvaluation $evaluation) use ($professionalId): void {
            $evaluation->setAttribute(
                'can_manage',
                $professionalId !== null
                    && (int) $evaluation->aeeRecord?->attendanceSession?->professional_id === (int) $professionalId,
            );
        });

        return Inertia::render(
            'SpecializedEducationalSupport/Students/AeeRecords/Index',
            [
                'can' => [
                    'view' => $user->can('aee-record.view'),
                    'update' => $user->can('aee-record.update'),
                    'delete' => $user->can('aee-record.delete'),
                    'view_all' => $user->can('aee-record.view-all'),
                ],
            ] + compact('student', 'aeeEvaluations') + $options->execute() + $this->listProps($request, ['professional_id', 'is_present']),
        );
    }

    public function studentShow(Student $student, AeeStudentEvaluation $evaluation, ListStudentAeeEvaluationsQuery $query, Request $request): InertiaResponse
    {
        $evaluation = $query->show($student, $evaluation, $this->user($request));
        $can = $this->managementAbilities($request, (int) $evaluation->aeeRecord->attendanceSession->professional_id);

        return Inertia::render('SpecializedEducationalSupport/Students/AeeRecords/Show', compact('student', 'evaluation', 'can'));
    }

    public function studentEdit(Student $student, AeeStudentEvaluation $evaluation, ListStudentAeeEvaluationsQuery $query, Request $request): InertiaResponse
    {
        $evaluation = $query->show($student, $evaluation, $this->user($request));
        if ((int) $evaluation->aeeRecord->attendanceSession->professional_id !== $this->professionalId($request)) {
            throw new AccessDeniedException('Apenas o profissional vinculado pode editar esta avaliação.');
        }

        return Inertia::render('SpecializedEducationalSupport/Students/AeeRecords/Edit', compact('student', 'evaluation'));
    }

    /** @throws Throwable */
    public function studentUpdate(UpdateAeeStudentEvaluationData $data, Student $student, AeeStudentEvaluation $evaluation, UpdateAeeStudentEvaluationAction $action, Request $request): RedirectResponse
    {
        $action->execute($student, $evaluation, $data, $this->professionalId($request));

        return redirect()->route(
            'specialized-educational-support.students.show',
            ['student' => $student, 'tab' => 'aee-attendance'],
        )->with('success', 'Avaliação do aluno atualizada com sucesso.');
    }

    /** @throws Throwable */
    public function studentDestroy(Student $student, AeeStudentEvaluation $evaluation, DeleteAeeStudentEvaluationAction $action, Request $request): RedirectResponse
    {
        $action->execute($student, $evaluation, $this->professionalId($request));

        return redirect()->route(
            'specialized-educational-support.students.show',
            ['student' => $student, 'tab' => 'aee-attendance'],
        )->with('success', 'Avaliação do aluno removida com sucesso.');
    }

    public function studentPdf(Student $student, AeeStudentEvaluation $evaluation, ListStudentAeeEvaluationsQuery $query, Request $request): Response
    {
        $evaluation = $query->show($student, $evaluation, $this->user($request));
        $aeeRecord = $evaluation->aeeRecord;
        $aeeRecord->setRelation('studentEvaluations', collect([$evaluation]));
        $session = $aeeRecord->attendanceSession;
        $professional = $session->professional;
        $pdf = Pdf::loadView('pages.specialized-educational-support.aee-records.pdf', compact('aeeRecord', 'session', 'professional'))->setPaper('a4', 'portrait');
        PdfPageNumberer::apply($pdf);

        return $pdf->stream("registro-aee-{$student->person->name}-{$session->session_date->format('d-m-Y')}.pdf");
    }

    public function studentHistoryPdf(Student $student, StudentAeeEvaluationsQuery $query, Request $request): Response
    {
        $student->loadMissing(['person', 'currentCourse.course']);
        $aeeEvaluations = $query->history($student, $this->user($request));
        $pdf = Pdf::loadView(
            'pages.specialized-educational-support.aee-records.student-history-pdf',
            compact('student', 'aeeEvaluations'),
        )->setPaper('a4', 'portrait');
        PdfPageNumberer::apply($pdf);

        return $pdf->stream("historico-atendimentos-aee-{$student->registration}.pdf");
    }

    /**
     * @param  list<string>  $filterKeys
     * @return array{filters: array<string, string>, can: array<string, bool>}
     */
    private function listProps(Request $request, array $filterKeys): array
    {
        $user = $this->user($request);

        return [
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
     * Reproduz as condições do legado: permissão da ação e autoria do
     * profissional vinculado ao agendamento.
     *
     * @return array{update: bool, delete: bool}
     */
    private function managementAbilities(Request $request, int $sessionProfessionalId): array
    {
        $user = $this->user($request);
        $isOwner = $user->professional_id !== null && (int) $user->professional_id === $sessionProfessionalId;

        return [
            'update' => $isOwner && $user->can('aee-record.update'),
            'delete' => $isOwner && $user->can('aee-record.delete'),
        ];
    }

    private function user(Request $request): User
    {
        $user = $request->user();
        if (! $user instanceof User) {
            throw new AccessDeniedException('Usuário autenticado inválido.');
        }

        return $user;
    }

    private function professionalId(Request $request): int
    {
        $professionalId = $this->user($request)->professional_id;
        if ($professionalId === null) {
            throw new AccessDeniedException('A ação exige um profissional vinculado ao usuário.');
        }

        return (int) $professionalId;
    }
}
