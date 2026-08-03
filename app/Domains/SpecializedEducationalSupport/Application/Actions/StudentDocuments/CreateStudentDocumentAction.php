<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Actions\StudentDocuments;

use App\Domains\SpecializedEducationalSupport\Application\Data\StudentDocuments\CreateStudentDocumentData;
use App\Domains\SpecializedEducationalSupport\Application\Queries\Semesters\CurrentSemesterQuery;
use App\Domains\SpecializedEducationalSupport\Domain\DTOs\StudentDocuments\CreateStudentDocumentDTO;
use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidStudentDocument;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;
use App\Domains\SpecializedEducationalSupport\Domain\Models\StudentDocument;
use App\Domains\SpecializedEducationalSupport\Infrastructure\Storage\StudentDocumentStorage;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class CreateStudentDocumentAction
{
    public function __construct(
        private StudentDocumentStorage $storage,
        private CurrentSemesterQuery $currentSemesterQuery,
    ) {}

    /**
     * @throws Throwable
     */
    public function execute(Student $student, CreateStudentDocumentData $data, int $uploaderId): StudentDocument
    {
        $student->ensureIsActive();
        $fileData = $this->storage->store(
            $data->file,
            (int) $student->getKey(),
            $data->type,
        );

        try {
            return DB::transaction(function () use ($student, $data, $uploaderId, $fileData): StudentDocument {
                $lockedStudent = Student::query()
                    ->lockForUpdate()
                    ->findOrFail($student->getKey());
                $lockedStudent->ensureIsActive();

                $semester = $this->currentSemesterQuery->execute();

                if ($semester === null) {
                    throw new InvalidStudentDocument('Não existe semestre atual configurado no sistema.');
                }

                $document = StudentDocument::register(
                    $lockedStudent,
                    $semester,
                    new CreateStudentDocumentDTO(
                        title: $data->title,
                        type: $data->type,
                        filePath: $fileData['file_path'],
                        originalName: $fileData['original_name'],
                        mimeType: $fileData['mime_type'],
                        fileSize: $fileData['file_size'],
                        uploadedBy: $uploaderId,
                    ),
                );
                $document->save();

                return $document->load(['student.person', 'semester', 'uploader']);
            });
        } catch (Throwable $exception) {
            $this->deleteWithoutFailing($fileData['file_path']);

            throw $exception;
        }
    }

    private function deleteWithoutFailing(?string $path): void
    {
        try {
            $this->storage->delete($path);
        } catch (Throwable $cleanupException) {
            $this->reportWithoutFailing($cleanupException);
        }
    }

    private function reportWithoutFailing(Throwable $exception): void
    {
        try {
            report($exception);
        } catch (Throwable) {
            // A falha ao reportar não pode mascarar a exceção original.
        }
    }
}
