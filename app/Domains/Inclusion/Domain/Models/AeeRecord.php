<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Domain\Models;

use App\Domains\Audit\Domain\Contracts\Auditable;
use App\Domains\Audit\Domain\ValueObjects\AuditedField;
use App\Domains\Audit\Infrastructure\Eloquent\AuditsChanges;
use App\Domains\Inclusion\Domain\DTOs\AeeRecords\CreateAeeRecordDTO;
use App\Domains\Inclusion\Domain\DTOs\AeeRecords\UpdateAeeRecordDTO;
use App\Domains\Inclusion\Domain\Exceptions\InvalidAeeRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class AeeRecord extends Model implements Auditable
{
    use AuditsChanges;
    use SoftDeletes;

    protected $table = 'aee_records';

    protected $fillable = [
        'attendance_session_id',
        'duration',
        'activities_performed',
        'strategies_used',
        'resources_used',
        'general_observations',
    ];

    public static function auditType(): string
    {
        return 'aee_record';
    }

    public static function auditLabel(): string
    {
        return 'Atendimento Educacional Especializado';
    }

    public static function auditedFields(): array
    {
        return [
            // O agendamento não tem coluna de nome; o histórico guarda o vínculo pelo id.
            'attendance_session_id' => AuditedField::number('ID do agendamento'),
            'duration' => AuditedField::text('Duração'),
            'activities_performed' => AuditedField::text('Atividades planejadas/realizadas'),
            'strategies_used' => AuditedField::text('Estratégias planejadas/utilizadas'),
            'resources_used' => AuditedField::text('Recursos planejados/utilizados'),
            'general_observations' => AuditedField::text('Observações gerais'),
        ];
    }

    public function auditDescription(): string
    {
        return "o atendimento AEE #{$this->getKey()} do agendamento #{$this->attendance_session_id}";
    }

    /** @throws InvalidAeeRecord */
    public static function register(Session $session, CreateAeeRecordDTO $data): self
    {
        if (! $session->exists) {
            throw new InvalidAeeRecord('O registro AEE deve ser vinculado a um agendamento persistido.');
        }

        return new self([
            'attendance_session_id' => $session->getKey(),
            ...self::attributesFrom($data),
        ]);
    }

    public function revise(UpdateAeeRecordDTO $data): void
    {
        $this->fill(self::attributesFrom($data));
    }

    public function attendanceSession(): BelongsTo
    {
        return $this->belongsTo(Session::class, 'attendance_session_id')->withTrashed();
    }

    public function studentEvaluations(): HasMany
    {
        return $this->hasMany(AeeStudentEvaluation::class, 'aee_record_id');
    }

    private static function attributesFrom(CreateAeeRecordDTO|UpdateAeeRecordDTO $data): array
    {
        return [
            'duration' => trim($data->duration),
            'activities_performed' => trim($data->activitiesPerformed),
            'strategies_used' => self::nullableText($data->strategiesUsed),
            'resources_used' => self::nullableText($data->resourcesUsed),
            'general_observations' => self::nullableText($data->generalObservations),
        ];
    }

    private static function nullableText(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
