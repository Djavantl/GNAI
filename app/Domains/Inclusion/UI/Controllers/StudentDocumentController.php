<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\UI\Controllers;

use App\Domains\Inclusion\Application\Actions\StudentDocuments\CreateStudentDocumentAction;
use App\Domains\Inclusion\Application\Actions\StudentDocuments\DeleteStudentDocumentAction;
use App\Domains\Inclusion\Application\Actions\StudentDocuments\UpdateStudentDocumentAction;
use App\Domains\Inclusion\Application\Data\StudentDocuments\CreateStudentDocumentData;
use App\Domains\Inclusion\Application\Data\StudentDocuments\ListStudentDocumentsData;
use App\Domains\Inclusion\Application\Data\StudentDocuments\UpdateStudentDocumentData;
use App\Domains\Inclusion\Application\Queries\StudentDocuments\ListStudentDocumentsQuery;
use App\Domains\Inclusion\Application\Queries\StudentDocuments\StudentDocumentFormQuery;
use App\Domains\Inclusion\Domain\Models\Student;
use App\Domains\Inclusion\Domain\Models\StudentDocument;
use App\Domains\Inclusion\Infrastructure\Storage\StudentDocumentStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

final class StudentDocumentController
{
    public function index(Student $student, ListStudentDocumentsData $filters, ListStudentDocumentsQuery $query, StudentDocumentFormQuery $form, Request $request): InertiaResponse
    {
        $student->loadMissing('person');
        $documents = $query->execute($student, $filters)->through(
            static fn (StudentDocument $document): array => [
                ...$document->toArray(),
                'type_label' => $document->type->label(),
                'semester_label' => $document->semester?->label,
            ],
        );
        $options = $form->forIndex($student);
        $user = $request->user();
        $toOptions = static fn (iterable $items): array => collect($items)
            ->except('')
            ->map(static fn (mixed $label, int|string $value): array => ['value' => (string) $value, 'label' => (string) $label])
            ->values()
            ->all();

        return Inertia::render('Inclusion/StudentDocuments/Index', [
            'documents' => $documents,
            'student' => $student,
            'types' => $toOptions($options['types']),
            'semesters' => $toOptions($options['semesters']),
            'filters' => [
                'title' => $filters->title ?? '',
                'type' => $filters->type?->value ?? '',
                'semester_id' => $filters->semesterId === null ? '' : (string) $filters->semesterId,
            ],
            'can' => [
                'create' => $user?->can('student-document.create') ?? false,
                'view' => $user?->can('student-document.view') ?? false,
                'update' => $user?->can('student-document.update') ?? false,
                'delete' => $user?->can('student-document.delete') ?? false,
            ],
        ]);
    }

    public function show(StudentDocument $studentDocument, StudentDocumentStorage $storage): StreamedResponse
    {
        return $storage->inline($studentDocument);
    }

    public function create(Student $student, StudentDocumentFormQuery $form): InertiaResponse
    {
        return Inertia::render(
            'Inclusion/StudentDocuments/Create',
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
            ->route('specialized-educational-support.students.show', [
                'student' => $student,
                'tab' => 'documents',
            ])
            ->with('success', 'Documento enviado com sucesso.');
    }

    public function edit(StudentDocument $studentDocument, StudentDocumentFormQuery $form): InertiaResponse
    {
        return Inertia::render(
            'Inclusion/StudentDocuments/Edit',
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
            ->route('specialized-educational-support.students.show', [
                'student' => $studentDocument->student_id,
                'tab' => 'documents',
            ])
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
            ->route('specialized-educational-support.students.show', [
                'student' => $studentId,
                'tab' => 'documents',
            ])
            ->with('success', 'Documento removido com sucesso.');
    }

    public function download(StudentDocument $studentDocument, StudentDocumentStorage $storage): StreamedResponse
    {
        return $storage->download($studentDocument);
    }
}
