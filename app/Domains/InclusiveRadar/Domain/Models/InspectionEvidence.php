<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Domain\Models;

use Database\Factories\Domains\InclusiveRadar\InspectionEvidenceFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[UseFactory(InspectionEvidenceFactory::class)]
final class InspectionEvidence extends Model
{
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
