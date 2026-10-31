<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Domain\Models;

use App\Domains\Audit\Domain\Contracts\Auditable;
use App\Domains\Audit\Domain\ValueObjects\AuditedField;
use App\Domains\Audit\Infrastructure\Eloquent\AuditsChanges;
use App\Domains\Auth\Domain\Models\User;
use App\Domains\Inclusion\Domain\DTOs\Barriers\CreateBarrierDTO;
use App\Domains\Inclusion\Domain\DTOs\Barriers\UpdateBarrierDTO;
use App\Domains\Inclusion\Domain\Enums\BarrierStatus;
use App\Domains\Inclusion\Domain\Enums\Priority;
use App\Domains\Inclusion\Domain\Exceptions\InvalidBarrier;
use Database\Factories\Domains\Inclusion\BarrierFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

#[UseFactory(BarrierFactory::class)]
final class Barrier extends Model implements Auditable
{
    use AuditsChanges;
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'registered_by_user_id',
        'institution_id',
        'barrier_category_id',
        'location_id',
        'affected_student_id',
        'affected_professional_id',
        'not_applicable',
        'affected_person_name',
        'affected_person_role',
        'is_anonymous',
        'priority',
        'identified_at',
        'resolved_at',
        'is_active',
        'latitude',
        'longitude',
        'location_specific_details',
    ];

    protected $casts = [
        'identified_at' => 'date',
        'resolved_at' => 'date',
        'is_active' => 'boolean',
        'is_anonymous' => 'boolean',
        'not_applicable' => 'boolean',
        'priority' => Priority::class,
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public static function auditType(): string
    {
        return 'barrier';
    }

    public static function auditLabel(): string
    {
        return 'Barreira';
    }

    public static function auditedFields(): array
    {
        return [
            'name' => AuditedField::text('Título do Relato'),
            'description' => AuditedField::text('Descrição Detalhada'),
            'registered_by_user_id' => AuditedField::reference('Relator', User::class),
            'institution_id' => AuditedField::reference('Campus / Unidade', Institution::class),
            'barrier_category_id' => AuditedField::reference('Categoria', BarrierCategory::class),
            'location_id' => AuditedField::reference('Local / Ponto de Referência', Location::class),
            'location_specific_details' => AuditedField::text('Complemento'),
            // Estudante e profissional não têm nome próprio (fica em people): a matrícula os identifica.
            'affected_student_id' => AuditedField::reference('Estudante', Student::class, 'person.name'),
            'affected_professional_id' => AuditedField::reference('Profissional', Professional::class, 'person.name'),
            'is_anonymous' => AuditedField::boolean('Relato Anônimo'),
            'not_applicable' => AuditedField::boolean('Relato Geral'),
            'affected_person_name' => AuditedField::text('Nome da Pessoa Impactada'),
            'affected_person_role' => AuditedField::text('Cargo da Pessoa Impactada'),
            'priority' => AuditedField::enum('Prioridade', Priority::class),
            'identified_at' => AuditedField::date('Data de Identificação'),
            'resolved_at' => AuditedField::date('Data de Resolução'),
            'latitude' => AuditedField::number('Latitude'),
            'longitude' => AuditedField::number('Longitude'),
            'is_active' => AuditedField::boolean('Ativo no Sistema'),
            'deficiencies' => AuditedField::list('Público Alvo', model: Deficiency::class),
        ];
    }

    public function auditDescription(): string
    {
        return "a barreira {$this->name}";
    }

    /** @return array<string, mixed> */
    public function auditExtraValues(): array
    {
        return $this->exists ? ['deficiencies' => $this->deficiencies()->pluck('deficiencies.id')->all()] : [];
    }

    /**
     * @throws InvalidBarrier
     */
    public static function register(CreateBarrierDTO $data): self
    {
        return new self(self::attributesFrom($data) + [
            'registered_by_user_id' => $data->registeredBy,
        ]);
    }

    /**
     * @throws InvalidBarrier
     */
    public function revise(UpdateBarrierDTO $data): void
    {
        $this->fill(self::attributesFrom($data));
    }

    public function resolveIfStatusRequires(?BarrierStatus $status): void
    {
        if ($status?->marksResolution()) {
            $this->resolved_at ??= now();

            return;
        }

        $this->resolved_at = null;
    }

    /**
     * @param  list<int>  $deficiencyIds
     *
     * @throws InvalidBarrier
     */
    public function assignAffectedAudiences(array $deficiencyIds): void
    {
        if ($deficiencyIds === []) {
            throw new InvalidBarrier('Selecione pelo menos um público afetado.');
        }

        if (! $this->exists) {
            throw new InvalidBarrier('O público afetado só pode ser atribuído a uma barreira persistida.');
        }

        $before = $this->deficiencies()->pluck('deficiencies.id')->all();
        $this->deficiencies()->sync(array_values(array_unique($deficiencyIds)));
        $this->recordAuditFieldChange('deficiencies', $before, $this->deficiencies()->pluck('deficiencies.id')->all());
    }

    public function latestStatus(): ?BarrierStatus
    {
        $status = Inspection::query()
            ->whereMorphedTo('inspectable', $this)
            ->orderByDesc('inspection_date')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->value('status');

        if ($status instanceof BarrierStatus) {
            return $status;
        }

        return is_string($status) ? BarrierStatus::tryFrom($status) : null;
    }

    public function registeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registered_by_user_id');
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class)->withTrashed();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(BarrierCategory::class, 'barrier_category_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class)->withTrashed();
    }

    public function affectedStudent(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'affected_student_id');
    }

    public function affectedProfessional(): BelongsTo
    {
        return $this->belongsTo(Professional::class, 'affected_professional_id');
    }

    public function deficiencies(): BelongsToMany
    {
        return $this->belongsToMany(
            Deficiency::class,
            'barrier_deficiency',
            'barrier_id',
            'deficiency_id',
        )->withTimestamps();
    }

    public function inspections(): MorphMany
    {
        return $this->morphMany(Inspection::class, 'inspectable')
            ->with('evidences')
            ->orderByDesc('inspection_date')
            ->orderByDesc('created_at');
    }

    public function getMorphClass(): string
    {
        return 'barrier';
    }

    /**
     * @throws InvalidBarrier
     */
    private static function attributesFrom(CreateBarrierDTO|UpdateBarrierDTO $data): array
    {
        self::ensureCoordinatesAreValid($data->latitude, $data->longitude);

        return self::sanitizeReporterAttributes([
            'name' => trim($data->name),
            'description' => self::nullableText($data->description),
            'institution_id' => $data->institutionId,
            'barrier_category_id' => $data->barrierCategoryId,
            'location_id' => $data->locationId,
            'affected_student_id' => $data->affectedStudentId,
            'affected_professional_id' => $data->affectedProfessionalId,
            'not_applicable' => $data->notApplicable,
            'affected_person_name' => self::nullableText($data->affectedPersonName),
            'affected_person_role' => self::nullableText($data->affectedPersonRole),
            'is_anonymous' => $data->isAnonymous,
            'priority' => $data->priority,
            'identified_at' => $data->identifiedAt,
            'is_active' => $data->isActive,
            'latitude' => $data->latitude,
            'longitude' => $data->longitude,
            'location_specific_details' => self::nullableText($data->locationSpecificDetails),
        ]);
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    private static function sanitizeReporterAttributes(array $attributes): array
    {
        $cleanFields = [
            'affected_student_id' => null,
            'affected_professional_id' => null,
            'affected_person_name' => null,
            'affected_person_role' => null,
            'is_anonymous' => false,
            'not_applicable' => false,
        ];

        if ($attributes['is_anonymous'] === true) {
            return array_merge($attributes, $cleanFields, ['is_anonymous' => true]);
        }

        if ($attributes['not_applicable'] === true) {
            return array_merge($attributes, $cleanFields, [
                'not_applicable' => true,
                'affected_person_name' => $attributes['affected_person_name'],
                'affected_person_role' => $attributes['affected_person_role'],
            ]);
        }

        return array_merge($attributes, [
            'is_anonymous' => false,
            'not_applicable' => false,
            'affected_person_name' => null,
            'affected_person_role' => null,
        ]);
    }

    /**
     * @throws InvalidBarrier
     */
    private static function ensureCoordinatesAreValid(?float $latitude, ?float $longitude): void
    {
        if ($latitude !== null && ($latitude < -90 || $latitude > 90)) {
            throw new InvalidBarrier('A latitude deve estar entre -90 e 90.');
        }

        if ($longitude !== null && ($longitude < -180 || $longitude > 180)) {
            throw new InvalidBarrier('A longitude deve estar entre -180 e 180.');
        }
    }

    private static function nullableText(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
