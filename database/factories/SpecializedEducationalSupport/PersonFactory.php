<?php

namespace Database\Factories\SpecializedEducationalSupport;

use App\Models\SpecializedEducationalSupport\Person;
use Illuminate\Database\Eloquent\Factories\Factory;

class PersonFactory extends Factory
{
    protected $model = Person::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'document' => $this->faker->unique()->numerify('###########'),
            'birth_date' => $this->faker->dateTimeBetween('-60 years', '-8 years')->format('Y-m-d'),
            'gender' => $this->faker->randomElement(['male', 'female', 'other', 'not_specified']),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->numerify('###########'),
            'address' => $this->faker->address(),
            'photo' => null,
        ];
    }
}
