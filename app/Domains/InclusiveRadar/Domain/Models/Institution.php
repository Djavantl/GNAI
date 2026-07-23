<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Domain\Models;

use App\Domains\InclusiveRadar\Domain\DTOs\Institutions\CreateInstitutionDTO;
use App\Domains\InclusiveRadar\Domain\DTOs\Institutions\UpdateInstitutionDTO;
use App\Domains\InclusiveRadar\Domain\Exceptions\InvalidInstitution;
use App\Models\InclusiveRadar\Barrier;
use App\Models\InclusiveRadar\Inspection;
use Database\Factories\Domains\InclusiveRadar\InstitutionFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

#[UseFactory(InstitutionFactory::class)]
final class Institution extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const int DEFAULT_ZOOM = 16;

    protected $table = 'institutions';

    protected $fillable = [
        'name',
        'short_name',
        'city',
        'state',
        'district',
        'address',
        'latitude',
        'longitude',
        'default_zoom',
        'is_active',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'default_zoom' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * @throws InvalidInstitution
     */
    public static function register(CreateInstitutionDTO $data): self
    {
        return new self(self::attributesFrom($data));
    }

    /**
     * @throws InvalidInstitution
     */
    public function revise(UpdateInstitutionDTO $data): void
    {
        $this->fill(self::attributesFrom($data));
    }

    public function latestInspection(): MorphOne
    {
        return $this->morphOne(Inspection::class, 'inspectable')
            ->latestOfMany('inspection_date');
    }

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }

    public function barriers(): HasMany
    {
        return $this->hasMany(Barrier::class);
    }

    /**
     * @throws InvalidInstitution
     */
    private static function attributesFrom(CreateInstitutionDTO|UpdateInstitutionDTO $data): array
    {
        return [
            'name' => self::normalizeText($data->name),
            'short_name' => self::normalizeNullableText($data->shortName),
            'city' => self::normalizeText($data->city),
            'state' => self::normalizeText($data->state),
            'district' => self::normalizeNullableText($data->district),
            'address' => self::normalizeNullableText($data->address),
            'latitude' => self::normalizeLatitude($data->latitude),
            'longitude' => self::normalizeLongitude($data->longitude),
            'default_zoom' => self::normalizeDefaultZoom($data->defaultZoom),
            'is_active' => $data->isActive,
        ];
    }

    private static function normalizeText(string $value): string
    {
        return trim($value);
    }

    private static function normalizeNullableText(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    /**
     * @throws InvalidInstitution
     */
    private static function normalizeLatitude(float $latitude): float
    {
        if ($latitude < -90 || $latitude > 90) {
            throw new InvalidInstitution('A latitude deve estar entre -90 e 90.');
        }

        return $latitude;
    }

    /**
     * @throws InvalidInstitution
     */
    private static function normalizeLongitude(float $longitude): float
    {
        if ($longitude < -180 || $longitude > 180) {
            throw new InvalidInstitution('A longitude deve estar entre -180 e 180.');
        }

        return $longitude;
    }

    /**
     * @throws InvalidInstitution
     */
    private static function normalizeDefaultZoom(?int $defaultZoom): int
    {
        $defaultZoom ??= self::DEFAULT_ZOOM;

        if ($defaultZoom < 0) {
            throw new InvalidInstitution('O zoom padrão não pode ser negativo.');
        }

        return $defaultZoom;
    }
}
