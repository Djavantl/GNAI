<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Domain\Models;

use App\Domains\InclusiveRadar\Domain\DTOs\AccessibleEducationalMaterials\CreateAccessibleEducationalMaterialDTO;
use App\Domains\InclusiveRadar\Domain\DTOs\AccessibleEducationalMaterials\UpdateAccessibleEducationalMaterialDTO;
use App\Domains\InclusiveRadar\Domain\Enums\ConservationState;
use App\Domains\InclusiveRadar\Domain\Enums\ResourceStatus;
use App\Domains\InclusiveRadar\Domain\Exceptions\InvalidAccessibleEducationalMaterial;
use App\Domains\InclusiveRadar\Domain\Exceptions\InvalidStock;
use App\Domains\InclusiveRadar\Domain\ValueObjects\Stock;
use App\Models\SpecializedEducationalSupport\Deficiency;
use Database\Factories\Domains\InclusiveRadar\AccessibleEducationalMaterialFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[UseFactory(AccessibleEducationalMaterialFactory::class)]
final class AccessibleEducationalMaterial extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'accessible_educational_materials';

    protected $fillable = [
        'name',
        'is_digital',
        'notes',
        'asset_code',
        'quantity',
        'quantity_available',
        'conservation_state',
        'is_loanable',
        'status',
        'is_active',
    ];

    protected $casts = [
        'is_digital' => 'boolean',
        'is_loanable' => 'boolean',
        'is_active' => 'boolean',
        'quantity' => 'integer',
        'quantity_available' => 'integer',
        'conservation_state' => ConservationState::class,
        'status' => ResourceStatus::class,
    ];

    /**
     * @throws InvalidAccessibleEducationalMaterial
     * @throws InvalidStock
     */
    public static function register(CreateAccessibleEducationalMaterialDTO $data): self
    {
        $stock = $data->isDigital
            ? Stock::notApplicable()
            : Stock::initial($data->quantity ?? 0);

        return new self([
            'name' => self::normalizeName($data->name),
            'is_digital' => $data->isDigital,
            'is_loanable' => $data->isLoanable,
            'quantity' => $stock->total(),
            'quantity_available' => $stock->available(),
            'asset_code' => $data->assetCode?->value(),
            'conservation_state' => $data->conservationState,
            'status' => $data->status,
            'notes' => $data->notes,
            'is_active' => $data->isActive,
        ]);
    }

    /**
     * @throws InvalidAccessibleEducationalMaterial
     * @throws InvalidStock
     */
    public function revise(UpdateAccessibleEducationalMaterialDTO $data): void
    {
        if ($data->openLoans > 0 && $this->status !== $data->status) {
            throw new InvalidAccessibleEducationalMaterial(
                'Não é possível alterar o status do item enquanto houver empréstimos ativos.'
            );
        }

        $stock = $data->isDigital
            ? Stock::notApplicable()
            : Stock::withOpenLoans($data->quantity ?? 0, $data->openLoans);

        $this->fill([
            'name' => self::normalizeName($data->name),
            'is_digital' => $data->isDigital,
            'is_loanable' => $data->isLoanable,
            'quantity' => $stock->total(),
            'quantity_available' => $stock->available(),
            'asset_code' => $data->assetCode?->value(),
            'conservation_state' => $data->conservationState,
            'status' => $data->status,
            'notes' => $data->notes,
            'is_active' => $data->isActive,
        ]);
    }

    public function stock(): Stock
    {
        return $this->is_digital
            ? Stock::notApplicable()
            : Stock::restore(
                total: $this->quantity,
                available: $this->quantity_available,
            );
    }

    public function deficiencies(): BelongsToMany
    {
        return $this->belongsToMany(
            Deficiency::class,
            'accessible_educational_material_deficiency',
            'accessible_educational_material_id',
            'deficiency_id',
        )->withTimestamps();
    }

    public function accessibilityFeatures(): BelongsToMany
    {
        return $this->belongsToMany(
            AccessibilityFeature::class,
            'accessible_educational_material_accessibility',
            'accessible_educational_material_id',
            'accessibility_feature_id',
        )->withTimestamps();
    }

    /**
     * @param list<int> $targetAudienceIds
     * @throws InvalidAccessibleEducationalMaterial
     */
    public function assignTargetAudience(array $targetAudienceIds): void
    {
        if ($targetAudienceIds === []) {
            throw new InvalidAccessibleEducationalMaterial(
                'Selecione pelo menos um público-alvo.'
            );
        }

        if (! $this->exists) {
            throw new InvalidAccessibleEducationalMaterial(
                'O público-alvo só pode ser atribuído a um material persistido.'
            );
        }

        $this->deficiencies()->sync(array_values(array_unique($targetAudienceIds)));
    }

    /**
     * @param list<int> $featureIds
     * @throws InvalidAccessibleEducationalMaterial
     */
    public function assignAccessibilityFeatures(array $featureIds): void
    {
        if (! $this->exists) {
            throw new InvalidAccessibleEducationalMaterial(
                'Os recursos de acessibilidade só podem ser atribuídos a um material persistido.'
            );
        }

        $this->accessibilityFeatures()->sync(array_values(array_unique($featureIds)));
    }

    public function inspections(): MorphMany
    {
        return $this->morphMany(Inspection::class, 'inspectable');
    }

    public function loans(): MorphMany
    {
        return $this->morphMany(Loan::class, 'loanable');
    }

    public function waitlists(): MorphMany
    {
        return $this->morphMany(Waitlist::class, 'waitlistable');
    }

    public function getMorphClass(): string
    {
        return 'accessible_educational_material';
    }

    /**
     * @throws InvalidAccessibleEducationalMaterial
     */
    private static function normalizeName(string $name): string
    {
        $name = trim($name);

        if ($name === '') {
            throw new InvalidAccessibleEducationalMaterial(
                'O nome do material pedagógico é obrigatório.'
            );
        }

        if (mb_strlen($name) > 255) {
            throw new InvalidAccessibleEducationalMaterial(
                'O nome do material pedagógico deve possuir no máximo 255 caracteres.'
            );
        }

        return $name;
    }
}
