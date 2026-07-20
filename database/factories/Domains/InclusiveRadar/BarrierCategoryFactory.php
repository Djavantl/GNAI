<?php

namespace Database\Factories\Domains\InclusiveRadar;

use App\Domains\InclusiveRadar\Domain\Models\BarrierCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class BarrierCategoryFactory extends Factory
{
    protected $model = BarrierCategory::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->words(2, true),
            'description' => $this->faker->sentence(),
            'blocks_map' => true,
            'is_active' => true,
        ];
    }

    public function inactive(): self
    {
        return $this->state(fn () => [
            'is_active' => false,
        ]);
    }

    public function active(): self
    {
        return $this->state(fn () => [
            'is_active' => true,
        ]);
    }

    public function blocksMap(): self
    {
        return $this->state(fn () => [
            'blocks_map' => true,
        ]);
    }

    public function doesNotBlockMap(): self
    {
        return $this->state(fn () => [
            'blocks_map' => false,
        ]);
    }
}
