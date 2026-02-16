<?php

namespace App\Models\SpecializedEducationalSupport;

use App\Enums\SpecializedEducationalSupport\StudentDocumentType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentDocument extends Model
{
    protected $fillable = [
        'student_id',
        'title',
        'type',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
        'semester_id',
        'version',
        'uploaded_by',
    ];

    protected $casts = [
        'type' => StudentDocumentType::class,
        'version' => 'integer',
        'file_size' => 'integer',
    ];

    /**
     * Relacionamento com o estudante.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    /**
     * Relacionamento com o usuário que fez o upload.
     */
    public function professional(): BelongsTo
    {
        return $this->belongsTo(Professional::class, 'uploaded_by');
    }
}