<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Domain\Models;

use App\Domains\Audit\Domain\Contracts\Auditable;
use App\Domains\Audit\Domain\ValueObjects\AuditedField;
use App\Domains\Audit\Infrastructure\Eloquent\AuditsChanges;
use App\Domains\SpecializedEducationalSupport\Domain\DTOs\AeeRecords\AeeStudentEvaluationDTO;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

final class AeeStudentEvaluation extends Model implements Auditable
{
    use AuditsChanges;
    use SoftDeletes;

    protected $table = 'aee_student_evaluations';

    protected $fillable = [
        'aee_record_id',
        'student_id',
        'is_present',
        'absence_reason',
        'adaptations_made',
        'student_participation',
        'development_evaluation',
        'progress_indicators',
        'recommendations',
        'next_session_adjustments',
    ];

    protected $casts = ['is_present' => 'boolean'];

    public static function auditType(): string
    {
        return 'aee_student_evaluation';
    }

    public static function auditLabel(): string
    {
        return 'Avaliação Individual AEE';
    }

    public static function auditedFields(): array
    {
        return [
            // O nome do aluno mora em `people`; o histórico guarda os vínculos pelo id.
            'aee_record_id' => AuditedField::number('ID do atendimento AEE'),
            'student_id' => AuditedField::reference('Aluno', Student::class, 'person.name'),
            'is_present' => AuditedField::boolean('Aluno presente'),
            'absence_reason' => AuditedField::text('Motivo da ausência'),
            'adaptations_made' => AuditedField::text('Adaptações para este aluno'),
            'student_participation' => AuditedField::text('Participação'),
            'development_evaluation' => AuditedField::text('Avaliação do desenvolvimento'),
            'progress_indicators' => AuditedField::text('Indicadores de progresso'),
            'recommendations' => AuditedField::text('Recomendações'),
            'next_session_adjustments' => AuditedField::text('Ajustes para o próximo atendimento AEE'),
        ];
    }

    public function auditDescription(): string
    {
        $studentName = $this->student?->person?->name;

        return $studentName !== null
            ? "a avaliação AEE #{$this->getKey()} de {$studentName}"
            : "a avaliação AEE #{$this->getKey()}";
    }

    public static function register(AeeRecord $aeeRecord, Student $student, AeeStudentEvaluationDTO $data): self
    {
        return new self([
            'aee_record_id' => $aeeRecord->getKey(),
            'student_id' => $student->getKey(),
            ...self::attributesFrom($data),
        ]);
    }

    public function revise(AeeStudentEvaluationDTO $data): void
    {
        $this->fill(self::attributesFrom($data));
    }

    public function aeeRecord(): BelongsTo
    {
        return $this->belongsTo(AeeRecord::class, 'aee_record_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function scopeOfStudent(Builder $query, int $studentId): Builder
    {
        return $query->where('student_id', $studentId);
    }

    private static function attributesFrom(AeeStudentEvaluationDTO $data): array
    {
        return [
            'is_present' => $data->isPresent,
            'absence_reason' => $data->isPresent ? null : self::nullableText($data->absenceReason),
            'adaptations_made' => $data->isPresent ? self::nullableText($data->adaptationsMade) : null,
            'student_participation' => $data->isPresent ? self::nullableText($data->studentParticipation) : null,
            'development_evaluation' => $data->isPresent ? self::nullableText($data->developmentEvaluation) : null,
            'progress_indicators' => $data->isPresent ? self::nullableText($data->progressIndicators) : null,
            'recommendations' => $data->isPresent ? self::nullableText($data->recommendations) : null,
            'next_session_adjustments' => $data->isPresent ? self::nullableText($data->nextSessionAdjustments) : null,
        ];
    }

    private static function nullableText(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
