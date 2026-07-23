<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Domain\Models;

use App\Domains\InclusiveRadar\Domain\DTOs\Locations\CreateLocationDTO;
use App\Domains\InclusiveRadar\Domain\DTOs\Locations\UpdateLocationDTO;
use App\Domains\InclusiveRadar\Domain\Exceptions\InvalidLocation;
use Database\Factories\Domains\InclusiveRadar\LocationFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * RF: cadastro dos pontos de referência/localizações vinculados à instituição.
 * Uso: mapa do radar, barreiras georreferenciadas e relatórios de locais.
 */
#[UseFactory(LocationFactory::class)]
class Location extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'locations';

    protected $fillable = [
        'institution_id',
        'name',
        'type',
        'description',
        'latitude',
        'longitude',
        'google_place_id',
        'is_active',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'is_active' => 'boolean',
    ];

    /**
     * @throws InvalidLocation
     */
    public static function register(CreateLocationDTO $data): self
    {
        return new self(self::attributesFrom($data));
    }

    /**
     * @throws InvalidLocation
     */
    public function revise(UpdateLocationDTO $data): void
    {
        $this->fill(self::attributesFrom($data));
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function barriers(): HasMany
    {
        return $this->hasMany(Barrier::class);
    }

    /**
     * @throws InvalidLocation
     */
    private static function attributesFrom(CreateLocationDTO|UpdateLocationDTO $data): array
    {
        return [
            'institution_id' => $data->institutionId,
            'name' => self::normalizeText($data->name),
            'type' => self::normalizeNullableText($data->type),
            'description' => self::normalizeNullableText($data->description),
            'latitude' => self::normalizeLatitude($data->latitude),
            'longitude' => self::normalizeLongitude($data->longitude),
            'google_place_id' => self::normalizeNullableText($data->googlePlaceId),
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
     * @throws InvalidLocation
     */
    private static function normalizeLatitude(float $latitude): float
    {
        if ($latitude < -90 || $latitude > 90) {
            throw new InvalidLocation('A latitude deve estar entre -90 e 90.');
        }

        return $latitude;
    }

    /**
     * @throws InvalidLocation
     */
    private static function normalizeLongitude(float $longitude): float
    {
        if ($longitude < -180 || $longitude > 180) {
            throw new InvalidLocation('A longitude deve estar entre -180 e 180.');
        }

        return $longitude;
    }
}
