<?php

namespace Database\Factories\SpecializedEducationalSupport;

use App\Enums\SpecializedEducationalSupport\StudentStatus;
use App\Models\SpecializedEducationalSupport\Person;
use App\Models\SpecializedEducationalSupport\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        return [
            'person_id' => Person::factory(),
            'registration' => $this->faker->unique()->numerify('MAT######'),
            'entry_date' => $this->faker->dateTimeBetween('-5 years', 'now')->format('Y-m-d'),
            'status' => $this->faker->randomElement(StudentStatus::cases()),
        ];
    }

    public function active(): self
    {
        return $this->state(fn () => [
            'status' => StudentStatus::ACTIVE,
        ]);
    }
}
