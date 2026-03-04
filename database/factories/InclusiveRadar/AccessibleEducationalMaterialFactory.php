<?php

namespace Database\Factories\InclusiveRadar;

use App\Models\InclusiveRadar\AccessibleEducationalMaterial;
use App\Enums\InclusiveRadar\ConservationState;
use App\Enums\InclusiveRadar\ResourceStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccessibleEducationalMaterialFactory extends Factory
{
    protected $model = AccessibleEducationalMaterial::class;

    public function definition(): array
    {
        $quantity = $this->faker->numberBetween(1, 20);

        return [
            'name' => 'MPA - ' . $this->faker->words(3, true),
            'is_digital' => $this->faker->boolean(40),
            'notes' => $this->faker->optional()->paragraph(),
            'asset_code' => strtoupper($this->faker->bothify('PAT-####')),
            'quantity' => $quantity,
            'quantity_available' => $this->faker->numberBetween(0, $quantity),
            'conservation_state' => $this->faker
                ->randomElement(ConservationState::cases()),
            'status' => $this->faker
                ->randomElement(ResourceStatus::cases()),
            'is_loanable' => $this->faker->boolean(70),
            'is_active' => $this->faker->boolean(90),
        ];
    }


    public function digital(): self
    {
        return $this->state(fn () => [
            'is_digital' => true,
        ]);
    }

    public function physical(): self
    {
        return $this->state(fn () => [
            'is_digital' => false,
        ]);
    }

    public function available(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'quantity_available' => max(1, $attributes['quantity']),
            ];
        });
    }

    public function unavailable(): self
    {
        return $this->state(fn () => [
            'quantity_available' => 0,
        ]);
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
}
