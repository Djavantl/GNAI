<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Domain\Models;

use App\Domains\SpecializedEducationalSupport\Domain\DTOs\Positions\CreatePositionDTO;
use App\Domains\SpecializedEducationalSupport\Domain\DTOs\Positions\UpdatePositionDTO;
use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidPosition;
use App\Models\Permission;
use Database\Factories\Domains\SpecializedEducationalSupport\PositionFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[UseFactory(PositionFactory::class)]
final class Position extends Model
{
    use HasFactory;

    protected $table = 'positions';

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function register(CreatePositionDTO $data): self
    {
        return new self([
            'name' => self::normalizeName($data->name),
            'description' => self::normalizeDescription($data->description),
            'is_active' => $data->isActive,
        ]);
    }

    public function revise(UpdatePositionDTO $data): void
    {
        $this->fill([
            'name' => self::normalizeName($data->name),
            'description' => self::normalizeDescription($data->description),
        ]);
    }

    /**
     * @param list<int> $permissionIds
     *
     * @throws InvalidPosition
     */
    public function assignPermissions(array $permissionIds): void
    {
        if (! $this->exists) {
            throw new InvalidPosition(
                'As permissões só podem ser atribuídas a um cargo persistido.'
            );
        }

        $this->permissions()->sync(array_values(array_unique($permissionIds)));
    }

    /**
     * @throws InvalidPosition
     */
    public function ensureCanBeDeactivated(bool $hasProfessionals): void
    {
        if ($hasProfessionals) {
            throw new InvalidPosition(
                'Este cargo está vinculado a um ou mais profissionais e não pode ser desativado.'
            );
        }
    }

    /**
     * @throws InvalidPosition
     */
    public function ensureCanBeDeleted(bool $hasProfessionals): void
    {
        if ($hasProfessionals) {
            throw new InvalidPosition(
                'Este cargo está vinculado a um ou mais profissionais e não pode ser removido.'
            );
        }
    }

    /**
     * @throws InvalidPosition
     */
    public function ensureIsActive(): void
    {
        if (! $this->is_active) {
            throw new InvalidPosition(
                'Este cargo está desativado e não pode ser vinculado a um profissional.'
            );
        }
    }

    public function activate(): void
    {
        $this->is_active = true;
    }

    public function deactivate(): void
    {
        $this->is_active = false;
    }

    public function professionals(): HasMany
    {
        return $this->hasMany(Professional::class, 'position_id');
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            Permission::class,
            'permission_position',
            'position_id',
            'permission_id',
        );
    }

    private static function normalizeName(string $name): string
    {
        return trim($name);
    }

    private static function normalizeDescription(?string $description): ?string
    {
        $description = trim((string) $description);

        return $description === '' ? null : $description;
    }
}
