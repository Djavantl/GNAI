<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Infrastructure\Storage;

use App\Domains\Inclusion\Domain\Enums\StudentDocumentType;
use App\Domains\Inclusion\Domain\Exceptions\InvalidStudentDocument;
use App\Domains\Inclusion\Domain\Models\StudentDocument;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

final class StudentDocumentStorage
{
    private const DISK = 'local';

    /**
     * @return array{file_path: string, original_name: string, mime_type: string|null, file_size: int|null}
     *
     * @throws InvalidStudentDocument
     */
    public function store(UploadedFile $file, int $studentId, StudentDocumentType $type): array
    {
        $path = $file->store(
            "student_documents/{$studentId}/{$type->value}",
            self::DISK,
        );

        if (! is_string($path) || $path === '') {
            throw new InvalidStudentDocument('Não foi possível armazenar o documento do aluno.');
        }

        return [
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize() ?: null,
        ];
    }

    public function delete(?string $path): void
    {
        if (filled($path) && ! $this->disk()->delete($path)) {
            throw new InvalidStudentDocument('Não foi possível excluir o arquivo do documento.');
        }
    }

    public function deleteQuietly(?string $path): void
    {
        try {
            $this->delete($path);
        } catch (Throwable $cleanupException) {
            try {
                report($cleanupException);
            } catch (Throwable) {
                // A falha na limpeza do arquivo não pode mascarar o resultado da operação.
            }
        }
    }

    /**
     * @throws InvalidStudentDocument
     */
    public function inline(StudentDocument $document): StreamedResponse
    {
        $this->ensureExists($document);

        return $this->disk()->response(
            $document->file_path,
            $document->original_name,
        );
    }

    /**
     * @throws InvalidStudentDocument
     */
    public function download(StudentDocument $document): StreamedResponse
    {
        $this->ensureExists($document);

        return $this->disk()->download(
            $document->file_path,
            $document->original_name,
        );
    }

    /**
     * @throws InvalidStudentDocument
     */
    private function ensureExists(StudentDocument $document): void
    {
        if (! $this->disk()->exists($document->file_path)) {
            throw new InvalidStudentDocument('O arquivo deste documento não foi encontrado.');
        }
    }

    private function disk(): FilesystemAdapter
    {
        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk(self::DISK);

        return $disk;
    }
}
