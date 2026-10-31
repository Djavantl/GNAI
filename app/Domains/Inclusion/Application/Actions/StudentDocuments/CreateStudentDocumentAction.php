<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Actions\StudentDocuments;

use App\Domains\Inclusion\Application\Data\StudentDocuments\CreateStudentDocumentData;
use App\Domains\Inclusion\Application\Queries\Semesters\CurrentSemesterQuery;
use App\Domains\Inclusion\Domain\DTOs\StudentDocuments\CreateStudentDocumentDTO;
use App\Domains\Inclusion\Domain\Exceptions\InvalidStudentDocument;
use App\Domains\Inclusion\Domain\Models\Student;
use App\Domains\Inclusion\Domain\Models\StudentDocument;
use App\Domains\Inclusion\Infrastructure\Storage\StudentDocumentStorage;
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
            $this->storage->deleteQuietly($fileData['file_path']);

            throw $exception;
        }
    }
}
