<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Domain\Models;

use App\Domains\Audit\Domain\Contracts\Auditable;
use App\Domains\Audit\Domain\ValueObjects\AuditedField;
use App\Domains\Audit\Infrastructure\Eloquent\AuditsChanges;
use Database\Factories\Domains\Inclusion\InspectionEvidenceFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[UseFactory(InspectionEvidenceFactory::class)]
final class InspectionEvidence extends Model implements Auditable
{
    use AuditsChanges;
    use HasFactory;

    protected $table = 'inspection_evidences';

    protected $fillable = [
        'inspection_id',
        'path',
        'original_name',
        'mime_type',
        'size',
    ];

    protected $casts = [
        'size' => 'integer',
    ];

    public static function auditType(): string
    {
        return 'inspection_evidence';
    }

    public static function auditLabel(): string
    {
        return 'Evidência de Inspeção';
    }

    public static function auditedFields(): array
    {
        return [
            'inspection_id' => AuditedField::number('Inspeção'),
            'path' => AuditedField::file('Arquivo'),
            'original_name' => AuditedField::text('Nome Original do Arquivo'),
            'mime_type' => AuditedField::text('Tipo do Arquivo'),
            'size' => AuditedField::number('Tamanho (bytes)'),
        ];
    }

    public function auditDescription(): string
    {
        return "a evidência {$this->displayName()} da inspeção #{$this->inspection_id}";
    }

    public function inspection(): BelongsTo
    {
        return $this->belongsTo(Inspection::class, 'inspection_id');
    }

    public function isImage(): bool
    {
        return str_starts_with((string) $this->mime_type, 'image/');
    }

    public function displayName(): string
    {
        return $this->original_name ?: basename((string) $this->path);
    }

    public function iconClass(): string
    {
        return match (true) {
            str_contains((string) $this->mime_type, 'pdf') => 'fa-file-pdf',
            str_contains((string) $this->mime_type, 'word'),
            str_contains((string) $this->mime_type, 'opendocument.text') => 'fa-file-word',
            str_contains((string) $this->mime_type, 'presentation'),
            str_contains((string) $this->mime_type, 'powerpoint'),
            str_contains((string) $this->mime_type, 'opendocument.presentation') => 'fa-file-powerpoint',
            str_contains((string) $this->mime_type, 'spreadsheet'),
            str_contains((string) $this->mime_type, 'excel'),
            str_contains((string) $this->mime_type, 'csv'),
            str_contains((string) $this->mime_type, 'opendocument.spreadsheet') => 'fa-file-excel',
            default => 'fa-file-alt',
        };
    }
}
