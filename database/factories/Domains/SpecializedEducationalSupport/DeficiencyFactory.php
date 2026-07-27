<?php

declare(strict_types=1);

namespace Database\Factories\Domains\SpecializedEducationalSupport;

use App\Domains\SpecializedEducationalSupport\Domain\Models\Deficiency;
use Illuminate\Database\Eloquent\Factories\Factory;

final class DeficiencyFactory extends Factory
{
    protected $model = Deficiency::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->words(2, true),
            'cid_code' => strtoupper($this->faker->unique()->bothify('?##.#')),
            'description' => $this->faker->optional()->paragraph(),
            'is_active' => true,
        ];
    }

    public function active(): self
    {
        return $this->state(fn (): array => [
            'is_active' => true,
        ]);
    }

    public function inactive(): self
    {
        return $this->state(fn (): array => [
            'is_active' => false,
        ]);
    }
}
