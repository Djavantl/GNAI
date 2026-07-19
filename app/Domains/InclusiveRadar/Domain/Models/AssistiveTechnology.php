<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Domain\Models;

use App\Domains\InclusiveRadar\Domain\DTOs\AssistiveTechnologies\CreateAssistiveTechnologyDTO;
use App\Domains\InclusiveRadar\Domain\DTOs\AssistiveTechnologies\UpdateAssistiveTechnologyDTO;
use App\Domains\InclusiveRadar\Domain\Enums\ConservationState;
use App\Domains\InclusiveRadar\Domain\Enums\ResourceStatus;
use App\Domains\InclusiveRadar\Domain\Exceptions\InvalidAssistiveTechnology;
use App\Domains\InclusiveRadar\Domain\Exceptions\ResourceHasOpenLoans;
use App\Domains\InclusiveRadar\Domain\ValueObjects\Stock;
use App\Models\SpecializedEducationalSupport\Deficiency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class AssistiveTechnology extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'is_digital',
        'notes',
        'asset_code',
        'quantity',
        'quantity_available',
        'conservation_state',
        'status',
        'is_loanable',
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

    public static function register(CreateAssistiveTechnologyDTO $data): self
    {
        $stock = $data->digital
            ? Stock::notApplicable()
            : Stock::initial($data->quantity ?? 0);

        return new self([
            'name' => self::normalizeName($data->name),
            'is_digital' => $data->digital,
            'is_loanable' => $data->loanable,
            'quantity' => $stock->total(),
            'quantity_available' => $stock->available(),
            'asset_code' => $data->assetCode?->value(),
            'conservation_state' => $data->conservationState,
            'status' => $data->status,
            'notes' => $data->notes,
            'is_active' => $data->active,
        ]);
    }

    public function revise(UpdateAssistiveTechnologyDTO $data): void
    {
        if ($data->openLoans > 0 && $this->status !== $data->status) {
            throw new InvalidAssistiveTechnology(
                'Não é possível alterar o status do item enquanto houver empréstimos ativos.'
            );
        }

        $stock = $data->digital
            ? Stock::notApplicable()
            : Stock::withOpenLoans($data->quantity ?? 0, $data->openLoans);

        $this->fill([
            'name' => self::normalizeName($data->name),
            'is_digital' => $data->digital,
            'is_loanable' => $data->loanable,
            'quantity' => $stock->total(),
            'quantity_available' => $stock->available(),
            'asset_code' => $data->assetCode?->value(),
            'conservation_state' => $data->conservationState,
            'status' => $data->status,
            'notes' => $data->notes,
            'is_active' => $data->active,
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

    public function ensureCanBeRemoved(bool $hasOpenLoans): void
    {
        if ($hasOpenLoans) {
            throw new ResourceHasOpenLoans;
        }
    }

    public function deficiencies(): BelongsToMany
    {
        return $this->belongsToMany(
            Deficiency::class,
            'assistive_technology_deficiency',
            'assistive_technology_id',
            'deficiency_id',
        )->withTimestamps();
    }

    /**
     * @param  list<int>  $targetAudienceIds
     */
    public function assignTargetAudience(array $targetAudienceIds): void
    {
        if ($targetAudienceIds === []) {
            throw new InvalidAssistiveTechnology(
                'Selecione pelo menos um público-alvo.'
            );
        }

        if (! $this->exists) {
            throw new InvalidAssistiveTechnology(
                'O público-alvo só pode ser atribuído a uma tecnologia persistida.'
            );
        }

        $this->deficiencies()->sync(array_values(array_unique($targetAudienceIds)));
    }

    public function inspections(): MorphMany
    {
        return $this->morphMany(Inspection::class, 'inspectable');
    }

    public function loans(): MorphMany
    {
        return $this->morphMany(Loan::class, 'loanable');
    }

    public function getMorphClass(): string
    {
        return 'assistive_technology';
    }

    private static function normalizeName(string $name): string
    {
        $name = trim($name);

        if ($name === '') {
            throw new InvalidAssistiveTechnology(
                'Informe o tipo da tecnologia assistiva.'
            );
        }

        if (mb_strlen($name) > 255) {
            throw new InvalidAssistiveTechnology(
                'O nome da tecnologia assistiva deve possuir no máximo 255 caracteres.'
            );
        }

        return $name;
    }
}
