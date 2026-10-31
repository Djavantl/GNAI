<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Domain\Models;

use App\Domains\Audit\Domain\Contracts\Auditable;
use App\Domains\Audit\Domain\ValueObjects\AuditedField;
use App\Domains\Audit\Infrastructure\Eloquent\AuditsChanges;
use App\Domains\Inclusion\Domain\DTOs\Institutions\CreateInstitutionDTO;
use App\Domains\Inclusion\Domain\DTOs\Institutions\UpdateInstitutionDTO;
use App\Domains\Inclusion\Domain\Exceptions\InvalidInstitution;
use Database\Factories\Domains\Inclusion\InstitutionFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

#[UseFactory(InstitutionFactory::class)]
final class Institution extends Model implements Auditable
{
    use AuditsChanges;
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

    public static function auditType(): string
    {
        return 'institution';
    }

    public static function auditLabel(): string
    {
        return 'Instituição';
    }

    public static function auditedFields(): array
    {
        return [
            'name' => AuditedField::text('Nome da Instituição'),
            'short_name' => AuditedField::text('Sigla / Nome Curto'),
            'city' => AuditedField::text('Cidade'),
            'state' => AuditedField::text('Estado'),
            'district' => AuditedField::text('Bairro / Distrito'),
            'address' => AuditedField::text('Rua / Logradouro'),
            'latitude' => AuditedField::number('Latitude Sede'),
            'longitude' => AuditedField::number('Longitude Sede'),
            'default_zoom' => AuditedField::number('Zoom Padrão'),
            'is_active' => AuditedField::boolean('Instituição Ativa'),
        ];
    }

    public function auditDescription(): string
    {
        return "a instituição {$this->name}";
    }

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

    public function deactivateLocations(): void
    {
        // Um a um (e não update em massa) para cada ponto desativado entrar na auditoria.
        $this->locations()
            ->where('is_active', true)
            ->get()
            ->each(fn (Location $location) => $location->update(['is_active' => false]));
    }

    public function deleteLocations(): void
    {
        // Um a um (e não delete em massa) para cada ponto excluído entrar na auditoria.
        $this->locations()
            ->get()
            ->each(fn (Location $location) => $location->delete());
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
