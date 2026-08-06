<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\UI\Controllers;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\SpecializedEducationalSupport\Application\Actions\PedagogicalRecords\CreatePedagogicalRecordAction;
use App\Domains\SpecializedEducationalSupport\Application\Actions\PedagogicalRecords\DeletePedagogicalRecordAction;
use App\Domains\SpecializedEducationalSupport\Application\Actions\PedagogicalRecords\UpdatePedagogicalRecordAction;
use App\Domains\SpecializedEducationalSupport\Application\Data\PedagogicalRecords\CreatePedagogicalRecordData;
use App\Domains\SpecializedEducationalSupport\Application\Data\PedagogicalRecords\ListPedagogicalRecordsData;
use App\Domains\SpecializedEducationalSupport\Application\Data\PedagogicalRecords\UpdatePedagogicalRecordData;
use App\Domains\SpecializedEducationalSupport\Application\Queries\AttendanceRecords\AttendanceRecordFilterOptionsQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\PedagogicalRecords\ListPedagogicalRecordsQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\PedagogicalRecords\PedagogicalRecordFormQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\PedagogicalRecords\ShowPedagogicalRecordQuery;
use App\Domains\SpecializedEducationalSupport\Domain\Models\PedagogicalRecord;
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

final class PedagogicalRecordController
{
    public function index(ListPedagogicalRecordsData $filters, ListPedagogicalRecordsQuery $query, AttendanceRecordFilterOptionsQuery $options, Request $request): View
    {
        $pedagogicalRecords = $query->execute($filters, $this->user($request));
        if ($request->ajax()) {
            return view('pages.specialized-educational-support.pedagogical-records.partials.table', compact('pedagogicalRecords'));
        }

        return view('pages.specialized-educational-support.pedagogical-records.index', compact('pedagogicalRecords') + $options->execute());
    }

    public function myRecords(ListPedagogicalRecordsData $filters, ListPedagogicalRecordsQuery $query, AttendanceRecordFilterOptionsQuery $options, Request $request): View
    {
        $pedagogicalRecords = $query->execute($filters, $this->user($request), onlyOwn: true);
        if ($request->ajax()) {
            return view('pages.specialized-educational-support.pedagogical-records.partials.table', compact('pedagogicalRecords'));
        }

        return view('pages.specialized-educational-support.pedagogical-records.my-records', compact('pedagogicalRecords') + $options->execute());
    }

    public function studentIndex(Student $student, ListPedagogicalRecordsData $filters, ListPedagogicalRecordsQuery $query, AttendanceRecordFilterOptionsQuery $options, Request $request): View
    {
        $student->loadMissing('person');
        $pedagogicalRecords = $query->execute($filters, $this->user($request), student: $student);
        if ($request->ajax()) {
            return view('pages.specialized-educational-support.students.pedagogical-records.partials.table', compact('student', 'pedagogicalRecords'));
        }

        return view('pages.specialized-educational-support.students.pedagogical-records.index', compact('student', 'pedagogicalRecords') + $options->execute());
    }

    public function create(Session $session, PedagogicalRecordFormQuery $form, Request $request): View
    {
        return view('pages.specialized-educational-support.pedagogical-records.create', $form->forCreation($session, $this->professionalId($request)));
    }

    /** @throws Throwable */
    public function store(CreatePedagogicalRecordData $data, CreatePedagogicalRecordAction $action, Request $request): RedirectResponse
    {
        $record = $action->execute($data, $this->professionalId($request));

        return redirect()->route('specialized-educational-support.pedagogical-records.show', $record)->with('success', 'Atendimento pedagógico criado com sucesso.');
    }

    public function show(PedagogicalRecord $pedagogicalRecord, ShowPedagogicalRecordQuery $query, Request $request): View
    {
        $pedagogicalRecord = $query->execute($pedagogicalRecord, $this->user($request));

        return view('pages.specialized-educational-support.pedagogical-records.show', compact('pedagogicalRecord'));
    }

    public function edit(PedagogicalRecord $pedagogicalRecord, PedagogicalRecordFormQuery $form, Request $request): View
    {
        return view('pages.specialized-educational-support.pedagogical-records.edit', $form->forUpdate($pedagogicalRecord, $this->professionalId($request)));
    }

    /** @throws Throwable */
    public function update(UpdatePedagogicalRecordData $data, PedagogicalRecord $pedagogicalRecord, UpdatePedagogicalRecordAction $action, Request $request): RedirectResponse
    {
        $pedagogicalRecord = $action->execute($pedagogicalRecord, $data, $this->professionalId($request));

        return redirect()->route('specialized-educational-support.pedagogical-records.show', $pedagogicalRecord)->with('success', 'Atendimento pedagógico atualizado com sucesso.');
    }

    /** @throws Throwable */
    public function destroy(PedagogicalRecord $pedagogicalRecord, DeletePedagogicalRecordAction $action, Request $request): RedirectResponse
    {
        $sessionId = $action->execute($pedagogicalRecord, $this->professionalId($request));

        return redirect()->route('specialized-educational-support.sessions.show', $sessionId)->with('success', 'Atendimento pedagógico removido com sucesso.');
    }

    public function pdf(PedagogicalRecord $pedagogicalRecord, ShowPedagogicalRecordQuery $query, Request $request): Response
    {
        $pedagogicalRecord = $query->execute($pedagogicalRecord, $this->user($request));
        $session = $pedagogicalRecord->attendanceSession;
        $student = $session->students->first();
        $professional = $session->professional;
        $pdf = Pdf::loadView('pages.specialized-educational-support.pedagogical-records.pdf', compact('pedagogicalRecord', 'session', 'student', 'professional'))->setPaper('a4', 'portrait');
        PdfPageNumberer::apply($pdf);

        return $pdf->stream("registro-pedagogico-{$session->session_date->format('d-m-Y')}.pdf");
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
