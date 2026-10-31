<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\UI\Controllers;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\Inclusion\Application\Actions\AeeRecords\CreateAeeRecordAction;
use App\Domains\Inclusion\Application\Actions\AeeRecords\DeleteAeeRecordAction;
use App\Domains\Inclusion\Application\Actions\AeeRecords\DeleteAeeStudentEvaluationAction;
use App\Domains\Inclusion\Application\Actions\AeeRecords\UpdateAeeRecordAction;
use App\Domains\Inclusion\Application\Actions\AeeRecords\UpdateAeeStudentEvaluationAction;
use App\Domains\Inclusion\Application\Data\AeeRecords\CreateAeeRecordData;
use App\Domains\Inclusion\Application\Data\AeeRecords\ListAeeRecordsData;
use App\Domains\Inclusion\Application\Data\AeeRecords\UpdateAeeRecordData;
use App\Domains\Inclusion\Application\Data\AeeRecords\UpdateAeeStudentEvaluationData;
use App\Domains\Inclusion\Application\Queries\AeeRecords\AeeRecordFormQuery;
use App\Domains\Inclusion\Application\Queries\AeeRecords\ListAeeRecordsQuery;
use App\Domains\Inclusion\Application\Queries\AeeRecords\ListStudentAeeEvaluationsQuery;
use App\Domains\Inclusion\Application\Queries\AeeRecords\ShowAeeRecordQuery;
use App\Domains\Inclusion\Application\Queries\AttendanceRecords\AttendanceRecordFilterOptionsQuery;
use App\Domains\Inclusion\Application\Queries\Students\StudentAeeEvaluationsQuery;
use App\Domains\Inclusion\Domain\Models\AeeRecord;
use App\Domains\Inclusion\Domain\Models\AeeStudentEvaluation;
use App\Domains\Inclusion\Domain\Models\Session;
use App\Domains\Inclusion\Domain\Models\Student;
use App\Domains\Inclusion\UI\Presenters\AeeRecordPagePresenter;
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
    public function index(ListAeeRecordsData $filters, ListAeeRecordsQuery $query, AttendanceRecordFilterOptionsQuery $options, AeeRecordPagePresenter $presenter, Request $request): InertiaResponse
    {
        $user = $this->user($request);
        $aeeRecords = $query->execute($filters, $user);

        return Inertia::render(
            'Inclusion/AeeRecords/Index',
            compact('aeeRecords') + $presenter->listing($options->execute(), $user, $request, ['student', 'professional_id', 'course_id', 'is_present']),
        );
    }

    public function myRecords(ListAeeRecordsData $filters, ListAeeRecordsQuery $query, AttendanceRecordFilterOptionsQuery $options, AeeRecordPagePresenter $presenter, Request $request): InertiaResponse
    {
        $user = $this->user($request);
        $aeeRecords = $query->execute($filters, $user, onlyOwn: true);

        return Inertia::render(
            'Inclusion/AeeRecords/MyRecords',
            compact('aeeRecords') + $presenter->listing($options->execute(), $user, $request, ['student', 'course_id', 'is_present']),
        );
    }

    public function create(Session $session, AeeRecordFormQuery $form, Request $request): InertiaResponse
    {
        return Inertia::render('Inclusion/AeeRecords/Create', $form->forCreation($session, $this->professionalId($request)));
    }

    /** @throws Throwable */
    public function store(CreateAeeRecordData $data, CreateAeeRecordAction $action, Request $request): RedirectResponse
    {
        $aeeRecord = $action->execute($data, $this->professionalId($request));

        return redirect()->route('specialized-educational-support.aee-records.show', $aeeRecord)->with('success', 'Registro do atendimento AEE criado com sucesso.');
    }

    public function show(AeeRecord $aeeRecord, ShowAeeRecordQuery $query, AeeRecordPagePresenter $presenter, Request $request): InertiaResponse
    {
        $user = $this->user($request);
        $aeeRecord = $query->execute($aeeRecord, $user);
        $session = $aeeRecord->attendanceSession;
        $can = $presenter->managementAbilities($user, (int) $session->professional_id);

        return Inertia::render('Inclusion/AeeRecords/Show', compact('aeeRecord', 'session', 'can'));
    }

    public function edit(AeeRecord $aeeRecord, AeeRecordFormQuery $form, Request $request): InertiaResponse
    {
        return Inertia::render('Inclusion/AeeRecords/Edit', $form->forUpdate($aeeRecord, $this->professionalId($request)));
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
        $pdf = Pdf::loadView('pages.inclusion.aee-records.pdf', compact('aeeRecord', 'session', 'professional', 'evaluations'))->setPaper('a4', 'portrait');
        PdfPageNumberer::apply($pdf);

        return $pdf->stream("registro-aee-{$session->session_date->format('d-m-Y')}-{$aeeRecord->id}.pdf");
    }

    public function studentIndex(Student $student, ListAeeRecordsData $filters, ListStudentAeeEvaluationsQuery $query, AttendanceRecordFilterOptionsQuery $options, AeeRecordPagePresenter $presenter, Request $request): InertiaResponse
    {
        $user = $this->user($request);
        $aeeEvaluations = $query->execute($student, $filters, $user);
        $student->loadMissing('person');

        return Inertia::render(
            'Inclusion/Students/AeeRecords/Index',
            $presenter->studentIndex($student, $aeeEvaluations, $options->execute(), $user, $request),
        );
    }

    public function studentShow(Student $student, AeeStudentEvaluation $evaluation, ListStudentAeeEvaluationsQuery $query, AeeRecordPagePresenter $presenter, Request $request): InertiaResponse
    {
        $user = $this->user($request);
        $evaluation = $query->show($student, $evaluation, $user);
        $can = $presenter->managementAbilities($user, (int) $evaluation->aeeRecord->attendanceSession->professional_id);

        return Inertia::render('Inclusion/Students/AeeRecords/Show', compact('student', 'evaluation', 'can'));
    }

    public function studentEdit(Student $student, AeeStudentEvaluation $evaluation, ListStudentAeeEvaluationsQuery $query, Request $request): InertiaResponse
    {
        $evaluation = $query->show($student, $evaluation, $this->user($request));
        if ((int) $evaluation->aeeRecord->attendanceSession->professional_id !== $this->professionalId($request)) {
            throw new AccessDeniedException('Apenas o profissional vinculado pode editar esta avaliação.');
        }

        return Inertia::render('Inclusion/Students/AeeRecords/Edit', compact('student', 'evaluation'));
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
        $pdf = Pdf::loadView('pages.inclusion.aee-records.pdf', compact('aeeRecord', 'session', 'professional'))->setPaper('a4', 'portrait');
        PdfPageNumberer::apply($pdf);

        return $pdf->stream("registro-aee-{$student->person->name}-{$session->session_date->format('d-m-Y')}.pdf");
    }

    public function studentHistoryPdf(Student $student, StudentAeeEvaluationsQuery $query, Request $request): Response
    {
        $student->loadMissing(['person', 'currentCourse.course']);
        $aeeEvaluations = $query->history($student, $this->user($request));
        $pdf = Pdf::loadView(
            'pages.inclusion.aee-records.student-history-pdf',
            compact('student', 'aeeEvaluations'),
        )->setPaper('a4', 'portrait');
        PdfPageNumberer::apply($pdf);

        return $pdf->stream("historico-atendimentos-aee-{$student->registration}.pdf");
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
