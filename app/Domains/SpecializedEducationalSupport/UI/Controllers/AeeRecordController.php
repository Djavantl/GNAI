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
use Illuminate\View\View;
use Throwable;

final class AeeRecordController
{
    public function index(ListAeeRecordsData $filters, ListAeeRecordsQuery $query, AttendanceRecordFilterOptionsQuery $options, Request $request): View
    {
        $aeeRecords = $query->execute($filters, $this->user($request));
        if ($request->ajax()) {
            return view('pages.specialized-educational-support.aee-records.partials.table', compact('aeeRecords'));
        }

        return view('pages.specialized-educational-support.aee-records.index', compact('aeeRecords') + $options->execute());
    }

    public function myRecords(ListAeeRecordsData $filters, ListAeeRecordsQuery $query, AttendanceRecordFilterOptionsQuery $options, Request $request): View
    {
        $aeeRecords = $query->execute($filters, $this->user($request), onlyOwn: true);
        if ($request->ajax()) {
            return view('pages.specialized-educational-support.aee-records.partials.table', compact('aeeRecords'));
        }

        return view('pages.specialized-educational-support.aee-records.my-records', compact('aeeRecords') + $options->execute());
    }

    public function create(Session $session, AeeRecordFormQuery $form, Request $request): View
    {
        return view('pages.specialized-educational-support.aee-records.create', $form->forCreation($session, $this->professionalId($request)));
    }

    /** @throws Throwable */
    public function store(CreateAeeRecordData $data, CreateAeeRecordAction $action, Request $request): RedirectResponse
    {
        $aeeRecord = $action->execute($data, $this->professionalId($request));

        return redirect()->route('specialized-educational-support.aee-records.show', $aeeRecord)->with('success', 'Registro do atendimento AEE criado com sucesso.');
    }

    public function show(AeeRecord $aeeRecord, ShowAeeRecordQuery $query, Request $request): View
    {
        $aeeRecord = $query->execute($aeeRecord, $this->user($request));
        $session = $aeeRecord->attendanceSession;

        return view('pages.specialized-educational-support.aee-records.show', compact('aeeRecord', 'session'));
    }

    public function edit(AeeRecord $aeeRecord, AeeRecordFormQuery $form, Request $request): View
    {
        return view('pages.specialized-educational-support.aee-records.edit', $form->forUpdate($aeeRecord, $this->professionalId($request)));
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

    public function studentIndex(Student $student, ListAeeRecordsData $filters, ListStudentAeeEvaluationsQuery $query, AttendanceRecordFilterOptionsQuery $options, Request $request): View
    {
        $aeeEvaluations = $query->execute($student, $filters, $this->user($request));
        if ($request->ajax()) {
            return view('pages.specialized-educational-support.students.aee-records.partials.table', compact('student', 'aeeEvaluations'));
        }

        return view('pages.specialized-educational-support.students.aee-records.index', compact('student', 'aeeEvaluations') + $options->execute());
    }

    public function studentShow(Student $student, AeeStudentEvaluation $evaluation, ListStudentAeeEvaluationsQuery $query, Request $request): View
    {
        $evaluation = $query->show($student, $evaluation, $this->user($request));

        return view('pages.specialized-educational-support.students.aee-records.show', compact('student', 'evaluation'));
    }

    public function studentEdit(Student $student, AeeStudentEvaluation $evaluation, ListStudentAeeEvaluationsQuery $query, Request $request): View
    {
        $evaluation = $query->show($student, $evaluation, $this->user($request));
        if ((int) $evaluation->aeeRecord->attendanceSession->professional_id !== $this->professionalId($request)) {
            throw new AccessDeniedException('Apenas o profissional vinculado pode editar esta avaliação.');
        }

        return view('pages.specialized-educational-support.students.aee-records.edit', compact('student', 'evaluation'));
    }

    /** @throws Throwable */
    public function studentUpdate(UpdateAeeStudentEvaluationData $data, Student $student, AeeStudentEvaluation $evaluation, UpdateAeeStudentEvaluationAction $action, Request $request): RedirectResponse
    {
        $action->execute($student, $evaluation, $data, $this->professionalId($request));

        return redirect()->route('specialized-educational-support.students.aee-records.show', [$student, $evaluation])->with('success', 'Avaliação do aluno atualizada com sucesso.');
    }

    /** @throws Throwable */
    public function studentDestroy(Student $student, AeeStudentEvaluation $evaluation, DeleteAeeStudentEvaluationAction $action, Request $request): RedirectResponse
    {
        $action->execute($student, $evaluation, $this->professionalId($request));

        return redirect()->route('specialized-educational-support.students.aee-records.index', $student)->with('success', 'Avaliação do aluno removida com sucesso.');
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
