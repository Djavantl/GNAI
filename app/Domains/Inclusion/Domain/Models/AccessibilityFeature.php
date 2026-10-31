<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Domain\Models;

use App\Domains\Audit\Domain\Contracts\Auditable;
use App\Domains\Audit\Domain\ValueObjects\AuditedField;
use App\Domains\Audit\Infrastructure\Eloquent\AuditsChanges;
use App\Domains\Inclusion\Domain\DTOs\AccessibilityFeatures\CreateAccessibilityFeatureDTO;
use App\Domains\Inclusion\Domain\DTOs\AccessibilityFeatures\UpdateAccessibilityFeatureDTO;
use Database\Factories\Domains\Inclusion\AccessibilityFeatureFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[UseFactory(AccessibilityFeatureFactory::class)]
final class AccessibilityFeature extends Model implements Auditable
{
    use AuditsChanges;
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

    public static function auditType(): string
    {
        return 'accessibility_feature';
    }

    public static function auditLabel(): string
    {
        return 'Recurso de Acessibilidade';
    }

    public static function auditedFields(): array
    {
        return [
            'name' => AuditedField::text('Nome do Recurso'),
            'description' => AuditedField::text('Descrição Detalhada'),
            'is_active' => AuditedField::boolean('Recurso Ativo'),
        ];
    }

    public function auditDescription(): string
    {
        return "o recurso de acessibilidade {$this->name}";
    }

    public static function register(CreateAccessibilityFeatureDTO $data): self
    {
        return new self([
            'name' => self::normalizeName($data->name),
            'description' => self::normalizeDescription($data->description),
            'is_active' => $data->isActive,
        ]);
    }

    public function revise(UpdateAccessibilityFeatureDTO $data): void
    {
        $this->fill([
            'name' => self::normalizeName($data->name),
            'description' => self::normalizeDescription($data->description),
            'is_active' => $data->isActive,
        ]);
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(
            Item::class,
            'item_accessibility_feature',
            'accessibility_feature_id',
            'item_id',
        )->withTimestamps();
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
