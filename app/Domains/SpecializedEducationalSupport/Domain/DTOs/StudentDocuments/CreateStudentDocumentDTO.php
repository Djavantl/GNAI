<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Domain\DTOs\StudentDocuments;

use App\Domains\SpecializedEducationalSupport\Domain\Enums\StudentDocumentType;

final readonly class CreateStudentDocumentDTO
{
    public function __construct(
        public string $title,
        public StudentDocumentType $type,
        public string $filePath,
        public string $originalName,
        public ?string $mimeType,
        public ?int $fileSize,
        public int $uploadedBy,
    ) {}
}
