<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Domain\Models;

use App\Domains\Audit\Domain\Contracts\Auditable;
use App\Domains\Audit\Domain\ValueObjects\AuditedField;
use App\Domains\Audit\Infrastructure\Eloquent\AuditsChanges;
use App\Domains\Inclusion\Domain\DTOs\Items\CreateItemDTO;
use App\Domains\Inclusion\Domain\DTOs\Items\UpdateItemDTO;
use App\Domains\Inclusion\Domain\Enums\ConservationState;
use App\Domains\Inclusion\Domain\Enums\ItemType;
use App\Domains\Inclusion\Domain\Enums\ResourceStatus;
use App\Domains\Inclusion\Domain\Exceptions\InvalidItem;
use App\Domains\Inclusion\Domain\Exceptions\InvalidStock;
use App\Domains\Inclusion\Domain\ValueObjects\Stock;
use Database\Factories\Domains\Inclusion\ItemFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[UseFactory(ItemFactory::class)]
final class Item extends Model implements Auditable
{
    use AuditsChanges;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'item_type',
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
        'item_type' => ItemType::class,
        'is_digital' => 'boolean',
        'is_loanable' => 'boolean',
        'is_active' => 'boolean',
        'quantity' => 'integer',
        'quantity_available' => 'integer',
        'conservation_state' => ConservationState::class,
        'status' => ResourceStatus::class,
    ];

    public static function auditType(): string
    {
        return 'item';
    }

    public static function auditLabel(): string
    {
        return 'Item';
    }

    public static function auditedFields(): array
    {
        return [
            'item_type' => AuditedField::enum('Tipo do Item', ItemType::class),
            'name' => AuditedField::text('Descrição do Item'),
            'is_digital' => AuditedField::boolean('Recurso Digital'),
            'notes' => AuditedField::richText('Descrição Detalhada'),
            'asset_code' => AuditedField::text('Patrimônio / Tombamento'),
            'quantity' => AuditedField::number('Quantidade Total'),
            'quantity_available' => AuditedField::number('Quantidade Disponível'),
            'conservation_state' => AuditedField::enum('Estado de Conservação', ConservationState::class),
            'status' => AuditedField::enum('Status do Recurso', ResourceStatus::class),
            'is_loanable' => AuditedField::boolean('Permite Empréstimos'),
            'is_active' => AuditedField::boolean('Ativo no Sistema'),
            'deficiencies' => AuditedField::list('Público Alvo', model: Deficiency::class),
            'accessibility_features' => AuditedField::list('Recursos de Acessibilidade', model: AccessibilityFeature::class),
        ];
    }

    public function auditDescription(): string
    {
        $type = $this->item_type instanceof ItemType ? mb_strtolower($this->item_type->label()) : 'item';

        return "o item {$this->name} ({$type})";
    }

    /** @return array<string, mixed> */
    public function auditExtraValues(): array
    {
        return $this->exists
            ? [
                'deficiencies' => $this->deficiencies()->pluck('deficiencies.id')->all(),
                'accessibility_features' => $this->accessibilityFeatures()->pluck('accessibility_features.id')->all(),
            ]
            : [];
    }

    /**
     * @throws InvalidStock
     */
    public static function register(CreateItemDTO $data): self
    {
        $stock = $data->isDigital
            ? Stock::notApplicable()
            : Stock::initial($data->quantity ?? 0);

        return new self([
            'item_type' => $data->itemType,
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
     * @throws InvalidItem
     * @throws InvalidStock
     */
    public function revise(UpdateItemDTO $data): void
    {
        if ($data->openLoans > 0 && $this->status !== $data->status) {
            throw new InvalidItem(
                'Não é possível alterar o status do item enquanto houver empréstimos ativos.'
            );
        }

        $stock = $data->isDigital
            ? Stock::notApplicable()
            : Stock::withOpenLoans($data->quantity ?? 0, $data->openLoans);

        $this->fill([
            'item_type' => $data->itemType,
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
            'item_deficiency',
            'item_id',
            'deficiency_id',
        )->withTimestamps();
    }

    public function accessibilityFeatures(): BelongsToMany
    {
        return $this->belongsToMany(
            AccessibilityFeature::class,
            'item_accessibility_feature',
            'item_id',
            'accessibility_feature_id',
        )->withTimestamps();
    }

    /**
     * @param  list<int>  $targetAudienceIds
     *
     * @throws InvalidItem
     */
    public function assignTargetAudience(array $targetAudienceIds): void
    {
        if ($targetAudienceIds === []) {
            throw new InvalidItem(
                'Selecione pelo menos um público-alvo.'
            );
        }

        if (! $this->exists) {
            throw new InvalidItem(
                'O público-alvo só pode ser atribuído a um item persistido.'
            );
        }

        $before = $this->deficiencies()->pluck('deficiencies.id')->all();
        $this->deficiencies()->sync(array_values(array_unique($targetAudienceIds)));
        $this->recordAuditFieldChange('deficiencies', $before, $this->deficiencies()->pluck('deficiencies.id')->all());
    }

    /**
     * @param  list<int>  $featureIds
     *
     * @throws InvalidItem
     */
    public function assignAccessibilityFeatures(array $featureIds): void
    {
        if (! $this->exists) {
            throw new InvalidItem(
                'Os recursos de acessibilidade só podem ser atribuídos a um item persistido.'
            );
        }

        $before = $this->accessibilityFeatures()->pluck('accessibility_features.id')->all();
        $this->accessibilityFeatures()->sync(array_values(array_unique($featureIds)));
        $this->recordAuditFieldChange(
            'accessibility_features',
            $before,
            $this->accessibilityFeatures()->pluck('accessibility_features.id')->all(),
        );
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
        return 'item';
    }

    private static function normalizeName(string $name): string
    {
        return trim($name);
    }
}
