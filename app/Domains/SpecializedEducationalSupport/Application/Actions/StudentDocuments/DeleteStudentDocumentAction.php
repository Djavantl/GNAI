<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Actions\StudentDocuments;

use App\Domains\SpecializedEducationalSupport\Domain\Models\StudentDocument;
use App\Domains\SpecializedEducationalSupport\Infrastructure\Storage\StudentDocumentStorage;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class DeleteStudentDocumentAction
{
    public function __construct(
        private StudentDocumentStorage $storage,
    ) {}

    /**
     * @throws Throwable
     */
    public function execute(StudentDocument $document): void
    {
        $filePath = DB::transaction(function () use ($document): string {
            $lockedDocument = StudentDocument::query()
                ->lockForUpdate()
                ->findOrFail($document->getKey());
            $filePath = $lockedDocument->file_path;
            $lockedDocument->delete();

            return $filePath;
        });

        $this->deleteWithoutFailing($filePath);
    }

    private function deleteWithoutFailing(?string $path): void
    {
        try {
            $this->storage->delete($path);
        } catch (Throwable $cleanupException) {
            try {
                report($cleanupException);
            } catch (Throwable) {
                // A falha ao reportar não pode alterar o resultado já persistido.
            }
        }
    }
}
