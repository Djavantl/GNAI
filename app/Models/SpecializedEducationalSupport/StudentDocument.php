<?php

namespace App\Models\SpecializedEducationalSupport;

use App\Enums\SpecializedEducationalSupport\StudentDocumentType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\Auditable; // 1. Importar a Trait
use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class StudentDocument extends Model
{
    use Auditable; // 2. Adicionar a Trait

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
     * Relacionamento com Logs de Auditoria
     */
    public function logs(): MorphMany
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }

    /**
     * Labels amigáveis para o Log e PDF
     */
    public static function getAuditLabels(): array
    {
        return [
            'title'         => 'Título do Documento',
            'type'          => 'Tipo de Documento',
            'original_name' => 'Nome do Arquivo',
            'file_size'     => 'Tamanho',
            'semester_id'   => 'Semestre Acadêmico',
            'version'       => 'Versão',
            'uploaded_by'   => 'Responsável pelo Upload',
        ];
    }

    /**
     * Formatação dos valores para o histórico
     */
    public static function formatAuditValue(string $field, $value): ?string
    {
        if ($field === 'type' && $value instanceof StudentDocumentType) {
            return $value->value; // Ou o método de tradução do seu Enum, ex: $value->label()
        }

        if ($field === 'file_size' && $value) {
            return round($value / 1024, 2) . ' KB';
        }

        if ($field === 'semester_id') {
            return \App\Models\SpecializedEducationalSupport\Semester::find($value)?->name ?? "ID: $value";
        }

        if ($field === 'uploaded_by') {
            return \App\Models\SpecializedEducationalSupport\Professional::find($value)?->person?->name ?? "ID: $value";
        }

        return null;
    }

    // RELACIONAMENTOS

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function professional(): BelongsTo
    {
        return $this->belongsTo(Professional::class, 'uploaded_by');
    }
}