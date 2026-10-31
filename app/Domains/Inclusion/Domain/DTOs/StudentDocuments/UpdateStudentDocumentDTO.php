<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Domain\DTOs\StudentDocuments;

use App\Domains\Inclusion\Domain\Enums\StudentDocumentType;

final readonly class UpdateStudentDocumentDTO
{
    public function __construct(
        public string $title,
        public StudentDocumentType $type,
        public string $filePath,
        public string $originalName,
        public ?string $mimeType,
        public ?int $fileSize,
    ) {}
}
