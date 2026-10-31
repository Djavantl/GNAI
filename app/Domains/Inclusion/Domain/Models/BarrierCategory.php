<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Domain\Models;

use App\Domains\Audit\Domain\Contracts\Auditable;
use App\Domains\Audit\Domain\ValueObjects\AuditedField;
use App\Domains\Audit\Infrastructure\Eloquent\AuditsChanges;
use App\Domains\Inclusion\Domain\DTOs\BarrierCategories\CreateBarrierCategoryDTO;
use App\Domains\Inclusion\Domain\DTOs\BarrierCategories\UpdateBarrierCategoryDTO;
use Database\Factories\Domains\Inclusion\BarrierCategoryFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[UseFactory(BarrierCategoryFactory::class)]
final class BarrierCategory extends Model implements Auditable
{
    use AuditsChanges;
    use HasFactory;
    use SoftDeletes;

    protected $table = 'barrier_categories';

    protected $fillable = [
        'name',
        'description',
        'is_active',
        'blocks_map',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'blocks_map' => 'boolean',
    ];

    public static function auditType(): string
    {
        return 'barrier_category';
    }

    public static function auditLabel(): string
    {
        return 'Categoria de Barreira';
    }

    public static function auditedFields(): array
    {
        return [
            'name' => AuditedField::text('Nome da Categoria'),
            'description' => AuditedField::text('Descrição Detalhada'),
            'blocks_map' => AuditedField::boolean('Bloquear Mapa'),
            'is_active' => AuditedField::boolean('Ativo no Sistema'),
        ];
    }

    public function auditDescription(): string
    {
        return "a categoria de barreira {$this->name}";
    }

    public static function register(CreateBarrierCategoryDTO $data): self
    {
        return new self([
            'name' => self::normalizeName($data->name),
            'description' => self::normalizeDescription($data->description),
            'blocks_map' => $data->blocksMap,
            'is_active' => $data->isActive,
        ]);
    }

    public function revise(UpdateBarrierCategoryDTO $data): void
    {
        $this->fill([
            'name' => self::normalizeName($data->name),
            'description' => self::normalizeDescription($data->description),
            'blocks_map' => $data->blocksMap,
            'is_active' => $data->isActive,
        ]);
    }

    public function barriers(): HasMany
    {
        return $this->hasMany(Barrier::class, 'barrier_category_id');
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
