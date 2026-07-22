<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Domain\Models;

use App\Domains\InclusiveRadar\Domain\DTOs\AccessibilityFeatures\CreateAccessibilityFeatureDTO;
use App\Domains\InclusiveRadar\Domain\DTOs\AccessibilityFeatures\UpdateAccessibilityFeatureDTO;
use App\Domains\InclusiveRadar\Domain\Exceptions\InvalidAccessibilityFeature;
use Database\Factories\Domains\InclusiveRadar\AccessibilityFeatureFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[UseFactory(AccessibilityFeatureFactory::class)]
final class AccessibilityFeature extends Model
{
    use HasFactory;

    protected $table = 'accessibility_features';

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * @throws InvalidAccessibilityFeature
     */
    public static function register(CreateAccessibilityFeatureDTO $data): self
    {
        return new self([
            'name' => self::normalizeName($data->name),
            'description' => self::normalizeDescription($data->description),
            'is_active' => $data->isActive,
        ]);
    }

    /**
     * @throws InvalidAccessibilityFeature
     */
    public function revise(UpdateAccessibilityFeatureDTO $data): void
    {
        $this->fill([
            'name' => self::normalizeName($data->name),
            'description' => self::normalizeDescription($data->description),
            'is_active' => $data->isActive,
        ]);
    }

    public function materials(): BelongsToMany
    {
        return $this->belongsToMany(
            AccessibleEducationalMaterial::class,
            'accessible_educational_material_accessibility',
            'accessibility_feature_id',
            'accessible_educational_material_id',
        )->withTimestamps();
    }

    /**
     * @throws InvalidAccessibilityFeature
     */
    private static function normalizeName(string $name): string
    {
        $name = trim($name);

        if ($name === '') {
            throw new InvalidAccessibilityFeature('O nome do recurso é obrigatório.');
        }

        if (mb_strlen($name) > 255) {
            throw new InvalidAccessibilityFeature('O nome do recurso deve possuir no máximo 255 caracteres.');
        }

        return $name;
    }

    /**
     * @throws InvalidAccessibilityFeature
     */
    private static function normalizeDescription(?string $description): ?string
    {
        $description = trim((string) $description);

        if (mb_strlen($description) > 1000) {
            throw new InvalidAccessibilityFeature('A descrição do recurso deve possuir no máximo 1000 caracteres.');
        }

        return $description === '' ? null : $description;
    }
}
