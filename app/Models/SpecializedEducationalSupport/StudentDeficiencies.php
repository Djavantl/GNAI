<?php

namespace App\Models\SpecializedEducationalSupport;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Auditable; 
use App\Models\AuditLog;        
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

class StudentDeficiencies extends Pivot
{
    use HasFactory, Auditable; 

    protected $table = 'students_deficiencies';

    protected $fillable = [
        'student_id',
        'deficiency_id',
        'severity',
        'uses_support_resources',
        'notes',
    ];
 
    public $incrementing = true;

    /**
     * Relacionamento com Logs de Auditoria
     */
    public function logs(): MorphMany
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }

    /**
     * Labels amigáveis para o Log
     */
    public static function getAuditLabels(): array
    {
        return [
            'deficiency_id'          => 'Deficiência',
            'severity'               => 'Severidade/Grau',
            'uses_support_resources' => 'Usa Recursos de Apoio',
            'notes'                  => 'Observações da Deficiência',
        ];
    }

    /**
     * Formatação dos valores para o Log e PDF
     */
    public static function formatAuditValue(string $field, $value): ?string
    {
        if ($field === 'deficiency_id') {
            return \App\Models\SpecializedEducationalSupport\Deficiency::find($value)?->name ?? "ID: $value";
        }

        if ($field === 'uses_support_resources') {
            return $value ? 'Sim' : 'Não';
        }

        if ($field === 'severity') {
            $options = [
                'low'    => 'Leve',
                'medium' => 'Moderada',
                'high'   => 'Severa',
            ];
            return $options[$value] ?? $value;
        }

        return null;
    }

    // Relacionamentos

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function deficiency()
    {
        return $this->belongsTo(Deficiency::class);
    }

    // Scopes para Filtros
    public function scopeDeficiencyId($query, ?int $deficiencyId)
    {
        if (!$deficiencyId) return $query;
        return $query->where('deficiency_id', $deficiencyId);
    }

    public function scopeSeverity($query, ?string $severity)
    {
        if (!$severity) return $query;
        return $query->where('severity', $severity);
    }

    public function scopeUsesSupportResources($query, $uses)
    {
        if ($uses === null || $uses === '') return $query;
        return $query->where('uses_support_resources', (bool) $uses);
    }
}