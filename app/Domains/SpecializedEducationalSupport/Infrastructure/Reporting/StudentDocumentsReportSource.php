<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Infrastructure\Reporting;

use App\Domains\Reporting\Domain\Enums\ReportColumnType;
use App\Domains\Reporting\Infrastructure\Sources\EloquentReportSource;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\StudentDocumentType;
use App\Domains\SpecializedEducationalSupport\Domain\Models\StudentDocument;

final class StudentDocumentsReportSource extends EloquentReportSource
{
    public function key(): string
    {
        return 'specialized-support.student-documents';
    }

    public function label(): string
    {
        return 'Documentos dos alunos';
    }

    protected function model(): string
    {
        return StudentDocument::class;
    }

    protected function with(): array
    {
        return ['student.person', 'semester', 'uploader'];
    }

    protected function definitions(): array
    {
        return [
            'student' => ['label' => 'Aluno', 'path' => 'student.person.name'],
            'title' => ['label' => 'Título'],
            'type' => ['label' => 'Tipo', 'type' => ReportColumnType::SELECT, 'options' => $this->enumOptions(StudentDocumentType::class)],
            'original_name' => ['label' => 'Nome do arquivo'], 'mime_type' => ['label' => 'Formato'],
            'file_size' => ['label' => 'Tamanho (bytes)'],
            'semester' => ['label' => 'Semestre', 'path' => 'semester.label'],
            'uploader' => ['label' => 'Enviado por', 'path' => 'uploader.name'],
            'created_at' => ['label' => 'Enviado em', 'type' => ReportColumnType::DATE],
        ];
    }

    protected function filterable(): array
    {
        return ['student', 'title', 'type', 'semester', 'uploader', 'created_at'];
    }
}
