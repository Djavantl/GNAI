<?php

namespace Database\Factories\Domains\InclusiveRadar;

use App\Domains\InclusiveRadar\Domain\Enums\BarrierStatus;
use App\Domains\InclusiveRadar\Domain\Enums\ConservationState;
use App\Domains\InclusiveRadar\Domain\Enums\InspectionType;
use App\Domains\InclusiveRadar\Domain\Models\AccessibleEducationalMaterial;
use App\Domains\InclusiveRadar\Domain\Models\AssistiveTechnology;
use App\Domains\InclusiveRadar\Domain\Models\Barrier;
use App\Domains\InclusiveRadar\Domain\Models\Inspection;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class InspectionFactory extends Factory
{
    protected $model = Inspection::class;

    public function configure(): static
    {
        return $this->forAssistiveTechnology();
    }

    public function definition(): array
    {
        return [
            'state' => ConservationState::GOOD,
            'status' => null,
            'inspection_date' => now(),
            'description' => $this->faker->optional()->sentence(),
            'type' => $this->faker->randomElement([
                InspectionType::INITIAL,
                InspectionType::PERIODIC,
            ]),
            'user_id' => User::factory(),
        ];
    }

    public function forBarrier(?Barrier $barrier = null): static
    {
        return $this->state(function () {
            return [
                'state' => null,
                'status' => $this->faker->randomElement(BarrierStatus::cases())->value,
                'type' => $this->faker->randomElement([
                    InspectionType::INITIAL,
                    InspectionType::PERIODIC,
                ]),
            ];
        })->for($barrier ?? Barrier::factory(), 'inspectable');
    }

    public function forAccessibleEducationalMaterial(?AccessibleEducationalMaterial $material = null): static
    {
        return $this->state(function () {
            return [
                'state' => $this->faker->randomElement(ConservationState::cases()),
                'status' => null,
            ];
        })->for($material ?? AccessibleEducationalMaterial::factory(), 'inspectable');
    }

    public function forAssistiveTechnology(?AssistiveTechnology $assistiveTechnology = null): static
    {
        return $this->state(function () {
            return [
                'state' => $this->faker->randomElement(ConservationState::cases()),
                'status' => null,
            ];
        })->for($assistiveTechnology ?? AssistiveTechnology::factory(), 'inspectable');
    }
}
