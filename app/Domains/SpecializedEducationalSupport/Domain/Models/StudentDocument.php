<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Domain\Models;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\SpecializedEducationalSupport\Domain\DTOs\StudentDocuments\CreateStudentDocumentDTO;
use App\Domains\SpecializedEducationalSupport\Domain\DTOs\StudentDocuments\UpdateStudentDocumentDTO;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\StudentDocumentType;
use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidStudentDocument;
use App\Models\AuditLog;
use App\Models\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

final class StudentDocument extends Model
{
    use Auditable;

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

    public static function getAuditLabels(): array
    {
        return [
            'title' => 'Título do Documento',
            'type' => 'Tipo de Documento',
            'original_name' => 'Nome do Arquivo',
            'file_size' => 'Tamanho',
            'semester_id' => 'Semestre Acadêmico',
            'uploaded_by' => 'Responsável pelo Upload',
        ];
    }

    public static function formatAuditValue(string $field, mixed $value): ?string
    {
        if ($field === 'type') {
            $type = $value instanceof StudentDocumentType
                ? $value
                : StudentDocumentType::tryFrom((string) $value);

            return $type?->label();
        }

        if ($field === 'file_size' && is_numeric($value)) {
            return round((float) $value / 1024, 2).' KB';
        }

        if ($field === 'semester_id') {
            return Semester::find($value)?->label ?? "ID: {$value}";
        }

        if ($field === 'uploaded_by') {
            return User::find($value)?->name ?? "ID: {$value}";
        }

        return null;
    }

    public function logs(): MorphMany
    {
        return $this->morphMany(AuditLog::class, 'auditable');
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
