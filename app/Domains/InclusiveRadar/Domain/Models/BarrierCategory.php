<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Domain\Models;

use App\Domains\InclusiveRadar\Domain\DTOs\BarrierCategories\CreateBarrierCategoryDTO;
use App\Domains\InclusiveRadar\Domain\DTOs\BarrierCategories\UpdateBarrierCategoryDTO;
use App\Domains\InclusiveRadar\Domain\Exceptions\InvalidBarrierCategory;
use App\Models\InclusiveRadar\Barrier;
use Database\Factories\Domains\InclusiveRadar\BarrierCategoryFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[UseFactory(BarrierCategoryFactory::class)]
final class BarrierCategory extends Model
{
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

    /**
     * @throws InvalidBarrierCategory
     */
    public static function register(CreateBarrierCategoryDTO $data): self
    {
        return new self([
            'name' => self::normalizeName($data->name),
            'description' => self::normalizeDescription($data->description),
            'blocks_map' => $data->blocksMap,
            'is_active' => $data->active,
        ]);
    }

    /**
     * @throws InvalidBarrierCategory
     */
    public function revise(UpdateBarrierCategoryDTO $data): void
    {
        $this->fill([
            'name' => self::normalizeName($data->name),
            'description' => self::normalizeDescription($data->description),
            'blocks_map' => $data->blocksMap,
            'is_active' => $data->active,
        ]);
    }

    /**
     * @throws InvalidBarrierCategory
     */
    public function ensureCanBeRemoved(bool $hasBlockingBarriers): void
    {
        if ($hasBlockingBarriers) {
            throw new InvalidBarrierCategory(
                'Esta categoria não pode ser excluída pois possui barreiras ativas.'
            );
        }
    }

    public function barriers(): HasMany
    {
        return $this->hasMany(Barrier::class, 'barrier_category_id');
    }

    /**
     * @throws InvalidBarrierCategory
     */
    private static function normalizeName(string $name): string
    {
        $name = trim($name);

        if ($name === '') {
            throw new InvalidBarrierCategory('O nome da categoria é obrigatório.');
        }

        if (mb_strlen($name) > 150) {
            throw new InvalidBarrierCategory('O nome da categoria deve possuir no máximo 150 caracteres.');
        }

        return $name;
    }

    private static function normalizeDescription(?string $description): ?string
    {
        $description = trim((string) $description);

        return $description === '' ? null : $description;
    }
}
