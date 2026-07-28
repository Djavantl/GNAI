<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\UI\Controllers;

use App\Domains\SpecializedEducationalSupport\Application\Actions\StudentDocuments\CreateStudentDocumentAction;
use App\Domains\SpecializedEducationalSupport\Application\Actions\StudentDocuments\DeleteStudentDocumentAction;
use App\Domains\SpecializedEducationalSupport\Application\Actions\StudentDocuments\UpdateStudentDocumentAction;
use App\Domains\SpecializedEducationalSupport\Application\Data\StudentDocuments\CreateStudentDocumentData;
use App\Domains\SpecializedEducationalSupport\Application\Data\StudentDocuments\ListStudentDocumentsData;
use App\Domains\SpecializedEducationalSupport\Application\Data\StudentDocuments\UpdateStudentDocumentData;
use App\Domains\SpecializedEducationalSupport\Application\Queries\StudentDocuments\ListStudentDocumentsQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\StudentDocuments\StudentDocumentFormQuery;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;
use App\Domains\SpecializedEducationalSupport\Domain\Models\StudentDocument;
use App\Domains\SpecializedEducationalSupport\Infrastructure\Storage\StudentDocumentStorage;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

final class StudentDocumentController extends Controller
{
    public function index(Student $student, ListStudentDocumentsData $filters, ListStudentDocumentsQuery $query, StudentDocumentFormQuery $form, Request $request): View
    {
        $student->loadMissing('person');
        $documents = $query->execute($student, $filters);
        $options = $form->forIndex($student);

        if ($request->ajax()) {
            return view(
                'pages.specialized-educational-support.student-documents.partials.table',
                compact('documents', 'student'),
            );
        }

        return view(
            'pages.specialized-educational-support.student-documents.index',
            compact('documents', 'student') + $options,
        );
    }

    public function show(StudentDocument $studentDocument, StudentDocumentStorage $storage): StreamedResponse
    {
        return $storage->inline($studentDocument);
    }

    public function create(Student $student, StudentDocumentFormQuery $form): View
    {
        return view(
            'pages.specialized-educational-support.student-documents.create',
            $form->forCreation($student),
        );
    }

    /**
     * @throws Throwable
     */
    public function store(CreateStudentDocumentData $data, Student $student, CreateStudentDocumentAction $action, Request $request): RedirectResponse
    {
        $action->execute(
            student: $student,
            data: $data,
            uploaderId: (int) $request->user()?->getAuthIdentifier(),
        );

        return redirect()
            ->route('specialized-educational-support.student-documents.index', $student)
            ->with('success', 'Documento enviado com sucesso.');
    }

    public function edit(StudentDocument $studentDocument, StudentDocumentFormQuery $form): View
    {
        return view(
            'pages.specialized-educational-support.student-documents.edit',
            $form->forUpdate($studentDocument),
        );
    }

    /**
     * @throws Throwable
     */
    public function update(UpdateStudentDocumentData $data, StudentDocument $studentDocument, UpdateStudentDocumentAction $action): RedirectResponse
    {
        $studentDocument = $action->execute($studentDocument, $data);

        return redirect()
            ->route(
                'specialized-educational-support.student-documents.index',
                $studentDocument->student_id,
            )
            ->with('success', 'Documento atualizado com sucesso.');
    }

    /**
     * @throws Throwable
     */
    public function destroy(StudentDocument $studentDocument, DeleteStudentDocumentAction $action): RedirectResponse
    {
        $studentId = $studentDocument->student_id;
        $action->execute($studentDocument);

        return redirect()
            ->route('specialized-educational-support.student-documents.index', $studentId)
            ->with('success', 'Documento removido com sucesso.');
    }

    public function download(StudentDocument $studentDocument, StudentDocumentStorage $storage): StreamedResponse
    {
        return $storage->download($studentDocument);
    }
}
