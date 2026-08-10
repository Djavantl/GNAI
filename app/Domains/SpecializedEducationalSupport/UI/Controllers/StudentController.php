<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\UI\Controllers;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\SpecializedEducationalSupport\Application\Actions\Students\CreateStudentAction;
use App\Domains\SpecializedEducationalSupport\Application\Actions\Students\DeleteStudentAction;
use App\Domains\SpecializedEducationalSupport\Application\Actions\Students\UpdateStudentAction;
use App\Domains\SpecializedEducationalSupport\Application\Data\Students\CreateStudentData;
use App\Domains\SpecializedEducationalSupport\Application\Data\Students\ListStudentsData;
use App\Domains\SpecializedEducationalSupport\Application\Data\Students\UpdateStudentData;
use App\Domains\SpecializedEducationalSupport\Application\Queries\Students\ListStudentsQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\Students\ShowStudentQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\Students\StudentAeeEvaluationsQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\Students\StudentFilterOptionsQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\Students\StudentFormQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\Students\StudentPdfQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\Students\StudentPedagogicalRecordsQuery;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;
use App\Shared\Infrastructure\Pdf\PdfPageNumberer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Throwable;

final class StudentController
{
    public function index(ListStudentsData $filters, ListStudentsQuery $query, StudentFilterOptionsQuery $options, Request $request): View
    {
        $user = $request->user();
        $teacherId = $user instanceof User ? $user->teacher_id : null;
        $students = $query->execute($filters, $teacherId);

        if ($request->ajax()) {
            return view(
                'pages.specialized-educational-support.students.partials.table',
                compact('students'),
            );
        }

        return view(
            'pages.specialized-educational-support.students.index',
            compact('students') + $options->execute(),
        );
    }

    public function show(Student $student, ShowStudentQuery $showStudent, StudentAeeEvaluationsQuery $aeeEvaluationsQuery, StudentPedagogicalRecordsQuery $pedagogicalRecordsQuery, Request $request): View
    {
        $user = $request->user();
        $authenticatedUser = $user instanceof User ? $user : null;
        $student = $showStudent->execute($student);
        $aeeEvaluations = $aeeEvaluationsQuery->execute($student, $authenticatedUser);
        $pedagogicalRecords = $pedagogicalRecordsQuery->execute($student, $authenticatedUser);

        return view(
            'pages.specialized-educational-support.students.show',
            compact('student', 'aeeEvaluations', 'pedagogicalRecords'),
        );
    }

    public function create(StudentFormQuery $form): View
    {
        return view(
            'pages.specialized-educational-support.students.create',
            $form->forCreation(),
        );
    }

    /**
     * @throws Throwable
     */
    public function store(CreateStudentData $data, CreateStudentAction $action): RedirectResponse
    {
        $student = $action->execute($data);

        return redirect()
            ->route('specialized-educational-support.students.show', $student)
            ->with('success', 'Aluno cadastrado com sucesso.');
    }

    public function edit(Student $student, StudentFormQuery $form): View
    {
        return view(
            'pages.specialized-educational-support.students.edit',
            $form->forUpdate($student),
        );
    }

    /**
     * @throws Throwable
     */
    public function update(UpdateStudentData $data, Student $student, UpdateStudentAction $action): RedirectResponse
    {
        $student = $action->execute($student, $data);

        return redirect()
            ->route('specialized-educational-support.students.show', $student)
            ->with('success', 'Aluno atualizado com sucesso.');
    }

    /**
     * @throws Throwable
     */
    public function destroy(Student $student, DeleteStudentAction $action): RedirectResponse
    {
        $action->execute($student);

        return redirect()
            ->route('specialized-educational-support.students.index')
            ->with('success', 'Aluno removido com sucesso.');
    }

    public function pdf(Student $student, StudentPdfQuery $query): Response
    {
        $student = $query->execute($student);

        $pdf = Pdf::loadView(
            'pages.specialized-educational-support.students.pdf',
            compact('student'),
        );

        PdfPageNumberer::apply($pdf);

        return $pdf->stream("ficha-aluno-{$student->registration}.pdf");
    }
}
