<?php

namespace Database\Factories\Domains\Inclusion;

use App\Domains\Inclusion\Domain\Enums\ConservationState;
use App\Domains\Inclusion\Domain\Enums\ItemType;
use App\Domains\Inclusion\Domain\Enums\ResourceStatus;
use App\Domains\Inclusion\Domain\Models\AccessibilityFeature;
use App\Domains\Inclusion\Domain\Models\Item;
use App\Domains\Inclusion\Domain\Models\Deficiency;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition(): array
    {
        $quantity = $this->faker->numberBetween(1, 20);
        $isDigital = $this->faker->boolean(40);

        $itemType = $this->faker->randomElement(ItemType::cases());

        return [
            'item_type' => $itemType,
            'name' => ($itemType === ItemType::AssistiveTechnology ? 'TA - ' : 'MPA - ').$this->faker->words(3, true),
            'is_digital' => $isDigital,
            'notes' => $this->faker->optional()->paragraph(),
            'asset_code' => strtoupper($this->faker->unique()->bothify('PAT-####')),
            'quantity' => $isDigital ? null : $quantity,
            'quantity_available' => $isDigital ? null : $this->faker->numberBetween(0, $quantity),
            'conservation_state' => $isDigital
                ? ConservationState::NOT_APPLICABLE
                : $this->faker->randomElement([
                    ConservationState::NEW,
                    ConservationState::GOOD,
                    ConservationState::REGULAR,
                ]),
            'status' => ResourceStatus::AVAILABLE,
            'is_loanable' => $isDigital ? false : $this->faker->boolean(70),
            'is_active' => true,
        ];
    }

    public function assistiveTechnology(): self
    {
        return $this->state(fn () => [
            'item_type' => ItemType::AssistiveTechnology,
        ]);
    }

    public function accessibleEducationalMaterial(): self
    {
        return $this->state(fn () => [
            'item_type' => ItemType::AccessibleEducationalMaterial,
        ]);
    }

    public function digital(): self
    {
        return $this->state(fn () => [
            'is_digital' => true,
            'quantity' => null,
            'quantity_available' => null,
            'conservation_state' => ConservationState::NOT_APPLICABLE,
            'is_loanable' => false,
            'status' => ResourceStatus::AVAILABLE,
        ]);
    }

    public function physical(): self
    {
        return $this->state(function (array $attributes) {
            $quantity = (int) ($attributes['quantity'] ?? $this->faker->numberBetween(1, 20));

            return [
                'is_digital' => false,
                'quantity' => $quantity,
                'quantity_available' => min(
                    (int) ($attributes['quantity_available'] ?? $quantity),
                    $quantity
                ),
                'conservation_state' => $attributes['conservation_state'] ?? ConservationState::GOOD,
            ];
        });
    }

    public function available(): self
    {
        return $this->state(function (array $attributes) {
            if (($attributes['is_digital'] ?? false) === true) {
                return [
                    'quantity_available' => null,
                    'status' => ResourceStatus::AVAILABLE,
                ];
            }

            return [
                'quantity_available' => max(1, (int) ($attributes['quantity'] ?? 1)),
                'status' => ResourceStatus::AVAILABLE,
            ];
        });
    }

    public function unavailable(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'quantity_available' => ($attributes['is_digital'] ?? false) ? null : 0,
                'status' => ResourceStatus::UNAVAILABLE,
            ];
        });
    }

    public function active(): self
    {
        return $this->state(fn () => [
            'is_active' => true,
        ]);
    }

    public function inactive(): self
    {
        return $this->state(fn () => [
            'is_active' => false,
        ]);
    }

    public function loanable(): self
    {
        return $this->state(fn () => [
            'is_loanable' => true,
        ]);
    }

    public function notLoanable(): self
    {
        return $this->state(fn () => [
            'is_loanable' => false,
        ]);
    }

    public function withStatus(ResourceStatus $status): self
    {
        return $this->state(fn () => [
            'status' => $status,
        ]);
    }

    public function withConservation(ConservationState $state): self
    {
        return $this->state(fn () => [
            'conservation_state' => $state,
        ]);
    }

    public function withDeficiencies(int $count = 1): self
    {
        return $this->afterCreating(function (Item $item) use ($count) {
            $deficiencies = Deficiency::factory()
                ->count(max(1, $count))
                ->create();

            $item->deficiencies()->sync($deficiencies->modelKeys());
        });
    }

    public function withAccessibilityFeatures(int $count = 1): self
    {
        return $this->afterCreating(function (Item $item) use ($count) {
            $features = AccessibilityFeature::factory()
                ->count(max(1, $count))
                ->create();

            $item->accessibilityFeatures()->sync($features->modelKeys());
        });
    }
}
