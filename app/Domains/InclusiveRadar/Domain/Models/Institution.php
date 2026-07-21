<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Domain\Models;

use App\Domains\InclusiveRadar\Domain\DTOs\Institutions\CreateInstitutionDTO;
use App\Domains\InclusiveRadar\Domain\DTOs\Institutions\UpdateInstitutionDTO;
use App\Domains\InclusiveRadar\Domain\Exceptions\InvalidInstitution;
use App\Models\InclusiveRadar\Barrier;
use App\Models\InclusiveRadar\Inspection;
use App\Models\InclusiveRadar\Location;
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
            'name' => self::normalizeRequiredText($data->name, 'O nome da instituição é obrigatório.', 255),
            'short_name' => self::normalizeNullableText($data->shortName, 100),
            'city' => self::normalizeRequiredText($data->city, 'A cidade é obrigatória.', 255),
            'state' => self::normalizeRequiredText($data->state, 'O estado é obrigatório.', 255),
            'district' => self::normalizeNullableText($data->district, 255),
            'address' => self::normalizeNullableText($data->address, 255),
            'latitude' => self::normalizeLatitude($data->latitude),
            'longitude' => self::normalizeLongitude($data->longitude),
            'default_zoom' => self::normalizeDefaultZoom($data->defaultZoom),
            'is_active' => $data->active,
        ];
    }

    /**
     * @throws InvalidInstitution
     */
    private static function normalizeRequiredText(string $value, string $message, int $maxLength): string
    {
        $value = trim($value);

        if ($value === '') {
            throw new InvalidInstitution($message);
        }

        if (mb_strlen($value) > $maxLength) {
            throw new InvalidInstitution("O campo deve possuir no máximo {$maxLength} caracteres.");
        }

        return $value;
    }

    /**
     * @throws InvalidInstitution
     */
    private static function normalizeNullableText(?string $value, int $maxLength): ?string
    {
        $value = trim((string) $value);

        if (mb_strlen($value) > $maxLength) {
            throw new InvalidInstitution("O campo deve possuir no máximo {$maxLength} caracteres.");
        }

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
        $defaultZoom ??= 16;

        if ($defaultZoom < 0) {
            throw new InvalidInstitution('O zoom padrão não pode ser negativo.');
        }

        return $defaultZoom;
    }
}
