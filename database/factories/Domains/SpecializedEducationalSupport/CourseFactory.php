<?php

declare(strict_types=1);

namespace Database\Factories\Domains\SpecializedEducationalSupport;

use App\Domains\SpecializedEducationalSupport\Domain\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

final class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->words(3, true),
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
