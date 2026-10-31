<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Domain\Models;

use App\Domains\Audit\Domain\Contracts\Auditable;
use App\Domains\Audit\Domain\ValueObjects\AuditedField;
use App\Domains\Audit\Infrastructure\Eloquent\AuditsChanges;
use App\Domains\Inclusion\Domain\DTOs\PedagogicalRecords\PedagogicalRecordDTO;
use App\Domains\Inclusion\Domain\Exceptions\InvalidPedagogicalRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class PedagogicalRecord extends Model implements Auditable
{
    use AuditsChanges;
    use SoftDeletes;

    protected $table = 'pedagogical_records';

    protected $fillable = [
        'attendance_session_id',
        'follow_up_reason',
        'duration',
        'is_present',
        'absence_reason',
        'systematic_pedagogical_follow_up_record',
        'strategies_and_resources_adopted',
        'referrals_made',
        'complementary_observations',
    ];

    protected $casts = [
        'is_present' => 'boolean',
    ];

    public static function auditType(): string
    {
        return 'pedagogical_record';
    }

    public static function auditLabel(): string
    {
        return 'Atendimento Pedagógico';
    }

    public static function auditedFields(): array
    {
        return [
            // Agendamento e responsáveis não têm coluna de nome própria; o histórico guarda os vínculos pelo id.
            'attendance_session_id' => AuditedField::number('ID do agendamento'),
            'follow_up_reason' => AuditedField::text('Motivo do acompanhamento'),
            'duration' => AuditedField::text('Período/duração do acompanhamento'),
            'is_present' => AuditedField::boolean('Estudante presente'),
            'absence_reason' => AuditedField::text('Motivo da ausência'),
            'systematic_pedagogical_follow_up_record' => AuditedField::text('Registro do acompanhamento pedagógico sistemático'),
            'strategies_and_resources_adopted' => AuditedField::text('Estratégias e recursos adotados'),
            'referrals_made' => AuditedField::text('Encaminhamentos realizados'),
            'complementary_observations' => AuditedField::text('Observações complementares'),
            'guardians' => AuditedField::list('Responsáveis participantes', model: Guardian::class, nameColumn: 'person.name'),
        ];
    }

    public function auditDescription(): string
    {
        return "o atendimento pedagógico #{$this->getKey()} do agendamento #{$this->attendance_session_id}";
    }

    /** @return array<string, mixed> */
    public function auditExtraValues(): array
    {
        return $this->exists ? ['guardians' => $this->guardianIds()] : [];
    }

    /** @throws InvalidPedagogicalRecord */
    public static function register(Session $session, PedagogicalRecordDTO $data): self
    {
        if (! $session->exists) {
            throw new InvalidPedagogicalRecord('O registro pedagógico deve ser vinculado a um agendamento persistido.');
        }

        return new self([
            'attendance_session_id' => $session->getKey(),
            ...self::attributesFrom($data),
        ]);
    }

    public function revise(PedagogicalRecordDTO $data): void
    {
        $this->fill(self::attributesFrom($data));
    }

    public function attendanceSession(): BelongsTo
    {
        return $this->belongsTo(Session::class, 'attendance_session_id')->withTrashed();
    }

    public function guardians(): BelongsToMany
    {
        return $this->belongsToMany(
            Guardian::class,
            'pedagogical_record_guardians',
            'pedagogical_record_id',
            'guardian_id',
        )->withTimestamps();
    }

    /** @param list<int> $guardianIds */
    public function syncGuardians(array $guardianIds): void
    {
        $before = $this->guardianIds();
        $this->guardians()->sync(array_values(array_unique($guardianIds)));
        $this->recordAuditFieldChange('guardians', $before, $this->guardianIds());
    }

    public function getGuardianNamesAttribute(): string
    {
        return $this->guardians->pluck('person.name')->filter()->join(', ');
    }

    public function getHasGuardiansAttribute(): bool
    {
        return $this->guardians->isNotEmpty();
    }

    /** @return list<int> */
    private function guardianIds(): array
    {
        return $this->guardians()->pluck('student_guardians.id')->map(fn (mixed $id): int => (int) $id)->all();
    }

    private static function attributesFrom(PedagogicalRecordDTO $data): array
    {
        $keepsPedagogicalContent = $data->isPresent || $data->withGuardians;

        return [
            'follow_up_reason' => trim($data->followUpReason),
            'duration' => trim($data->duration),
            'is_present' => $data->isPresent,
            'absence_reason' => $data->isPresent ? null : self::nullableText($data->absenceReason),
            'systematic_pedagogical_follow_up_record' => $keepsPedagogicalContent ? self::nullableText($data->systematicPedagogicalFollowUpRecord) : null,
            'strategies_and_resources_adopted' => $keepsPedagogicalContent ? self::nullableText($data->strategiesAndResourcesAdopted) : null,
            'referrals_made' => $keepsPedagogicalContent ? self::nullableText($data->referralsMade) : null,
            'complementary_observations' => $keepsPedagogicalContent ? self::nullableText($data->complementaryObservations) : null,
        ];
    }

    private static function nullableText(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
