<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Domain\Models;

use App\Domains\Audit\Domain\Contracts\Auditable;
use App\Domains\Audit\Domain\ValueObjects\AuditedField;
use App\Domains\Audit\Infrastructure\Eloquent\AuditsChanges;
use App\Domains\Auth\Domain\Models\User;
use App\Domains\Inclusion\Domain\DTOs\StudentDocuments\CreateStudentDocumentDTO;
use App\Domains\Inclusion\Domain\DTOs\StudentDocuments\UpdateStudentDocumentDTO;
use App\Domains\Inclusion\Domain\Enums\StudentDocumentType;
use App\Domains\Inclusion\Domain\Exceptions\InvalidStudentDocument;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class StudentDocument extends Model implements Auditable
{
    use AuditsChanges;

    protected $table = 'student_documents';

    protected $fillable = [
        'student_id',
        'title',
        'type',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
        'semester_id',
        'uploaded_by',
    ];

    protected $casts = [
        'type' => StudentDocumentType::class,
        'file_size' => 'integer',
    ];

    public static function auditType(): string
    {
        return 'student_document';
    }

    public static function auditLabel(): string
    {
        return 'Documento do Aluno';
    }

    public static function auditedFields(): array
    {
        return [
            'student_id' => AuditedField::reference('Aluno', Student::class, 'person.name'),
            'title' => AuditedField::text('Título do Documento'),
            'type' => AuditedField::enum('Tipo de Documento', StudentDocumentType::class),
            'file_path' => AuditedField::file('Arquivo'),
            'original_name' => AuditedField::text('Nome original do arquivo'),
            'semester_id' => AuditedField::reference('Semestre', Semester::class, 'label'),
            'uploaded_by' => AuditedField::reference('Enviado por', User::class),
        ];
    }

    /** Metadados técnicos derivados do arquivo e versão sem uso na interface. */
    public static function unauditedFields(): array
    {
        return ['mime_type', 'file_size', 'version'];
    }

    public function auditDescription(): string
    {
        $studentName = $this->student?->person?->name ?? "#{$this->student_id}";

        return "o documento {$this->title} do aluno {$studentName}";
    }

    /**
     * @throws InvalidStudentDocument
     */
    public static function register(Student $student, Semester $semester, CreateStudentDocumentDTO $data): self
    {
        if (! $student->exists || ! $semester->exists) {
            throw new InvalidStudentDocument(
                'O documento deve ser vinculado a um aluno e a um semestre persistidos.'
            );
        }

        return new self([
            'student_id' => $student->getKey(),
            'semester_id' => $semester->getKey(),
            'uploaded_by' => $data->uploadedBy,
            ...self::attributesFrom($data),
        ]);
    }

    public function revise(UpdateStudentDocumentDTO $data): void
    {
        $this->fill(self::attributesFrom($data));
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * @return array<string, string|int|null>
     */
    private static function attributesFrom(CreateStudentDocumentDTO|UpdateStudentDocumentDTO $data): array
    {
        return [
            'title' => trim($data->title),
            'type' => $data->type->value,
            'file_path' => $data->filePath,
            'original_name' => $data->originalName,
            'mime_type' => $data->mimeType,
            'file_size' => $data->fileSize,
        ];
    }
}
