<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\UI\Controllers;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\Inclusion\Application\Actions\PedagogicalRecords\CreatePedagogicalRecordAction;
use App\Domains\Inclusion\Application\Actions\PedagogicalRecords\DeletePedagogicalRecordAction;
use App\Domains\Inclusion\Application\Actions\PedagogicalRecords\UpdatePedagogicalRecordAction;
use App\Domains\Inclusion\Application\Data\PedagogicalRecords\CreatePedagogicalRecordData;
use App\Domains\Inclusion\Application\Data\PedagogicalRecords\ListPedagogicalRecordsData;
use App\Domains\Inclusion\Application\Data\PedagogicalRecords\UpdatePedagogicalRecordData;
use App\Domains\Inclusion\Application\Queries\AttendanceRecords\AttendanceRecordFilterOptionsQuery;
use App\Domains\Inclusion\Application\Queries\PedagogicalRecords\ListPedagogicalRecordsQuery;
use App\Domains\Inclusion\Application\Queries\PedagogicalRecords\PedagogicalRecordFormQuery;
use App\Domains\Inclusion\Application\Queries\PedagogicalRecords\ShowPedagogicalRecordQuery;
use App\Domains\Inclusion\Application\Queries\Students\StudentPedagogicalRecordsQuery;
use App\Domains\Inclusion\Domain\Models\PedagogicalRecord;
use App\Domains\Inclusion\Domain\Models\Session;
use App\Domains\Inclusion\Domain\Models\Student;
use App\Shared\Application\Exceptions\AccessDeniedException;
use App\Shared\Infrastructure\Pdf\PdfPageNumberer;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Throwable;

final class PedagogicalRecordController
{
    public function index(ListPedagogicalRecordsData $filters, ListPedagogicalRecordsQuery $query, AttendanceRecordFilterOptionsQuery $options, Request $request): InertiaResponse
    {
        $pedagogicalRecords = $query->execute($filters, $this->user($request));

        return Inertia::render('Inclusion/PedagogicalRecords/Index', $this->listingProps($pedagogicalRecords, $options, $request));
    }

    public function myRecords(ListPedagogicalRecordsData $filters, ListPedagogicalRecordsQuery $query, AttendanceRecordFilterOptionsQuery $options, Request $request): InertiaResponse
    {
        $pedagogicalRecords = $query->execute($filters, $this->user($request), onlyOwn: true);

        return Inertia::render('Inclusion/PedagogicalRecords/MyRecords', $this->listingProps($pedagogicalRecords, $options, $request));
    }

    public function studentIndex(Student $student, ListPedagogicalRecordsData $filters, ListPedagogicalRecordsQuery $query, AttendanceRecordFilterOptionsQuery $options, Request $request): InertiaResponse
    {
        $student->loadMissing('person');
        $pedagogicalRecords = $query->execute($filters, $this->user($request), student: $student);

        return Inertia::render(
            'Inclusion/Students/PedagogicalRecords/Index',
            $this->listingProps($pedagogicalRecords, $options, $request) + [
                'student' => [
                    'id' => $student->getKey(),
                    'name' => $student->person?->name,
                ],
            ],
        );
    }

    public function create(Session $session, PedagogicalRecordFormQuery $form, Request $request): InertiaResponse
    {
        return Inertia::render('Inclusion/PedagogicalRecords/Create', $form->forCreation($session, $this->professionalId($request)));
    }

    /** @throws Throwable */
    public function store(CreatePedagogicalRecordData $data, CreatePedagogicalRecordAction $action, Request $request): RedirectResponse
    {
        $record = $action->execute($data, $this->professionalId($request));

        return redirect()->route('specialized-educational-support.pedagogical-records.show', $record)->with('success', 'Atendimento pedagógico criado com sucesso.');
    }

    public function show(PedagogicalRecord $pedagogicalRecord, ShowPedagogicalRecordQuery $query, Request $request): InertiaResponse
    {
        $user = $this->user($request);
        $pedagogicalRecord = $query->execute($pedagogicalRecord, $user);

        return Inertia::render(
            'Inclusion/PedagogicalRecords/Show',
            $this->showProps($pedagogicalRecord, $user) + [
                'returnStudentId' => $request->integer('student_id'),
            ],
        );
    }

    public function edit(PedagogicalRecord $pedagogicalRecord, PedagogicalRecordFormQuery $form, Request $request): InertiaResponse
    {
        return Inertia::render(
            'Inclusion/PedagogicalRecords/Edit',
            $form->forUpdate($pedagogicalRecord, $this->professionalId($request)) + [
                'returnStudentId' => $request->integer('student_id'),
            ],
        );
    }

    /** @throws Throwable */
    public function update(UpdatePedagogicalRecordData $data, PedagogicalRecord $pedagogicalRecord, UpdatePedagogicalRecordAction $action, Request $request): RedirectResponse
    {
        $pedagogicalRecord = $action->execute($pedagogicalRecord, $data, $this->professionalId($request));

        $parameters = ['pedagogicalRecord' => $pedagogicalRecord];

        // Edição aberta pelo prontuário: a visualização mantém a origem para o Voltar.
        if ($request->integer('student_id') > 0) {
            $parameters['student_id'] = $request->integer('student_id');
        }

        return redirect()->route('specialized-educational-support.pedagogical-records.show', $parameters)->with('success', 'Atendimento pedagógico atualizado com sucesso.');
    }

    /** @throws Throwable */
    public function destroy(PedagogicalRecord $pedagogicalRecord, DeletePedagogicalRecordAction $action, Request $request): RedirectResponse
    {
        $sessionId = $action->execute($pedagogicalRecord, $this->professionalId($request));

        if ($request->integer('student_id') > 0) {
            return redirect()->route(
                'specialized-educational-support.students.show',
                [
                    'student' => $request->integer('student_id'),
                    'tab' => 'pedagogical-attendance',
                ],
            )->with('success', 'Atendimento pedagógico removido com sucesso.');
        }

        return redirect()->route('specialized-educational-support.sessions.show', $sessionId)->with('success', 'Atendimento pedagógico removido com sucesso.');
    }

    public function pdf(PedagogicalRecord $pedagogicalRecord, ShowPedagogicalRecordQuery $query, Request $request): Response
    {
        $pedagogicalRecord = $query->execute($pedagogicalRecord, $this->user($request));
        $session = $pedagogicalRecord->attendanceSession;
        $student = $session->students->first();
        $professional = $session->professional;
        $pdf = Pdf::loadView('pages.inclusion.pedagogical-records.pdf', compact('pedagogicalRecord', 'session', 'student', 'professional'))->setPaper('a4', 'portrait');
        PdfPageNumberer::apply($pdf);

        return $pdf->stream("registro-pedagogico-{$session->session_date->format('d-m-Y')}.pdf");
    }

    public function studentHistoryPdf(Student $student, StudentPedagogicalRecordsQuery $query, Request $request): Response
    {
        $student->loadMissing(['person', 'currentCourse.course']);
        $pedagogicalRecords = $query->history($student, $this->user($request));
        $pdf = Pdf::loadView(
            'pages.inclusion.pedagogical-records.student-history-pdf',
            compact('student', 'pedagogicalRecords'),
        )->setPaper('a4', 'portrait');
        PdfPageNumberer::apply($pdf);

        return $pdf->stream("historico-atendimentos-pedagogicos-{$student->registration}.pdf");
    }

    /**
     * Presentation-only props for the React listings: lean rows, filter options, current filters and abilities.
     *
     * @param  LengthAwarePaginator<int, PedagogicalRecord>  $pedagogicalRecords
     * @return array<string, mixed>
     */
    private function listingProps(LengthAwarePaginator $pedagogicalRecords, AttendanceRecordFilterOptionsQuery $options, Request $request): array
    {
        $user = $this->user($request);
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
        $filterOptions = $options->execute();
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
     * Presentation-only props for the React details page, with the same data and authorship rules used by the Blade view.
     *
     * @return array<string, mixed>
     */
    private function showProps(PedagogicalRecord $pedagogicalRecord, User $user): array
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
