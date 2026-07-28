<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\StudentDocuments;

use App\Domains\SpecializedEducationalSupport\Domain\Enums\StudentDocumentType;
use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidStudentDocument;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Semester;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;
use App\Domains\SpecializedEducationalSupport\Domain\Models\StudentDocument;

final class StudentDocumentFormQuery
{
    public function forIndex(Student $student): array
    {
        return [
            'semesters' => Semester::query()
                ->orderByDesc('year')
                ->orderByDesc('term')
                ->pluck('label', 'id')
                ->prepend('Semestre (Todos)', ''),
            'types' => collect(StudentDocumentType::labels())
                ->prepend('Tipo (Todos)', '')
                ->all(),
        ];
    }

    /**
     * @throws InvalidStudentDocument
     */
    public function forCreation(Student $student): array
    {
        $student->ensureIsActive();

        return [
            'student' => $student->loadMissing('person'),
            'types' => StudentDocumentType::labels(),
            'semester' => $this->currentSemester(),
        ];
    }

    public function forUpdate(StudentDocument $document): array
    {
        $student = $document->student;
        $student->ensureIsActive();

        return [
            'studentDocument' => $document,
            'student' => $student->loadMissing('person'),
            'types' => StudentDocumentType::labels(),
            'semester' => Semester::query()
                ->where('is_current', true)
                ->first(),
        ];
    }

    /**
     * @throws InvalidStudentDocument
     */
    private function currentSemester(): Semester
    {
        $semester = Semester::query()
            ->where('is_current', true)
            ->first();

        if (! $semester instanceof Semester) {
            throw new InvalidStudentDocument(
                'Não existe semestre atual configurado no sistema.'
            );
        }

        return $semester;
    }
}
