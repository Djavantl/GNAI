<?php

declare(strict_types=1);

namespace Database\Factories\Domains\SpecializedEducationalSupport;

use App\Domains\SpecializedEducationalSupport\Domain\Enums\Gender;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Person;
use Illuminate\Database\Eloquent\Factories\Factory;

final class PersonFactory extends Factory
{
    protected $model = Person::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'document' => null,
            'birth_date' => $this->faker->dateTimeBetween('-80 years', '-5 years')->format('Y-m-d'),
            'gender' => $this->faker->randomElement(Gender::cases()),
            'email' => $this->faker->boolean(90)
                ? $this->faker->unique()->safeEmail()
                : null,
            'phone' => $this->faker->optional()->numerify('###########'),
            'address' => $this->faker->optional()->address(),
            'photo' => null,
        ];
    }
}
