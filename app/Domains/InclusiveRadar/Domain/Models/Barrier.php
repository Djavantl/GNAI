<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Domain\Models;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\InclusiveRadar\Domain\DTOs\Barriers\CreateBarrierDTO;
use App\Domains\InclusiveRadar\Domain\DTOs\Barriers\UpdateBarrierDTO;
use App\Domains\InclusiveRadar\Domain\Enums\BarrierStatus;
use App\Domains\InclusiveRadar\Domain\Exceptions\InvalidBarrier;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Deficiency;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Professional;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;
use App\Enums\Priority;
use Database\Factories\Domains\InclusiveRadar\BarrierFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

#[UseFactory(BarrierFactory::class)]
final class Barrier extends Model
{
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

        $this->deficiencies()->sync(array_values(array_unique($deficiencyIds)));
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
