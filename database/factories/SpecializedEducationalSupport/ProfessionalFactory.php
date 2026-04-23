<?php

namespace Database\Factories\SpecializedEducationalSupport;

use App\Models\SpecializedEducationalSupport\Person;
use App\Models\SpecializedEducationalSupport\Position;
use App\Models\SpecializedEducationalSupport\Professional;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfessionalFactory extends Factory
{
    protected $model = Professional::class;

    public function definition(): array
    {
        return [
            'person_id' => Person::factory(),
            'position_id' => Position::factory(),
            'registration' => $this->faker->unique()->numerify('PROF######'),
            'entry_date' => $this->faker->dateTimeBetween('-10 years', 'now')->format('Y-m-d'),
            'status' => $this->faker->randomElement(['active', 'inactive']),
        ];
    }

    public function active(): self
    {
        return $this->state(fn () => [
            'status' => 'active',
        ]);
    }
}
