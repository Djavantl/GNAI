<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Actions\StudentDocuments;

use App\Domains\SpecializedEducationalSupport\Application\Data\StudentDocuments\UpdateStudentDocumentData;
use App\Domains\SpecializedEducationalSupport\Domain\DTOs\StudentDocuments\UpdateStudentDocumentDTO;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;
use App\Domains\SpecializedEducationalSupport\Domain\Models\StudentDocument;
use App\Domains\SpecializedEducationalSupport\Infrastructure\Storage\StudentDocumentStorage;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class UpdateStudentDocumentAction
{
    public function __construct(
        private StudentDocumentStorage $storage,
    ) {}

    /**
     * @throws Throwable
     */
    public function execute(StudentDocument $document, UpdateStudentDocumentData $data): StudentDocument
    {
        $newFileData = $data->file !== null
            ? $this->storage->store(
                $data->file,
                $document->student_id,
                $data->type,
            )
            : null;
        $oldFilePath = null;

        try {
            $updatedDocument = DB::transaction(function () use ($document, $data, $newFileData, &$oldFilePath): StudentDocument {
                $lockedDocument = StudentDocument::query()
                    ->lockForUpdate()
                    ->findOrFail($document->getKey());
                $student = Student::query()
                    ->lockForUpdate()
                    ->findOrFail($lockedDocument->student_id);
                $student->ensureIsActive();

                if ($newFileData !== null) {
                    $oldFilePath = $lockedDocument->file_path;
                    $filePath = $newFileData['file_path'];
                    $originalName = $newFileData['original_name'];
                    $mimeType = $newFileData['mime_type'];
                    $fileSize = $newFileData['file_size'];
                } else {
                    $filePath = $lockedDocument->file_path;
                    $originalName = $lockedDocument->original_name;
                    $mimeType = $lockedDocument->mime_type;
                    $fileSize = $lockedDocument->file_size;
                }

                $lockedDocument->revise(new UpdateStudentDocumentDTO(
                    title: $data->title,
                    type: $data->type,
                    filePath: $filePath,
                    originalName: $originalName,
                    mimeType: $mimeType,
                    fileSize: $fileSize,
                ));
                $lockedDocument->save();

                return $lockedDocument->load(['student.person', 'semester', 'uploader']);
            });
        } catch (Throwable $exception) {
            if ($newFileData !== null) {
                $this->deleteWithoutFailing($newFileData['file_path']);
            }

            throw $exception;
        }

        if ($oldFilePath !== null) {
            $this->deleteWithoutFailing($oldFilePath);
        }

        return $updatedDocument;
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
            // A falha ao reportar não pode alterar o resultado já persistido.
        }
    }
}
