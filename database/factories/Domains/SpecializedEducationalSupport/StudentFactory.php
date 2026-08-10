<?php

declare(strict_types=1);

namespace Database\Factories\Domains\SpecializedEducationalSupport;

use App\Domains\SpecializedEducationalSupport\Domain\Enums\StudentStatus;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Person;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

final class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        return [
            'person_id' => Person::factory(),
            'registration' => $this->faker->unique()->bothify('MAT######'),
            'entry_date' => $this->faker->optional()->dateTimeBetween('-5 years', 'now')?->format('Y-m-d'),
            'status' => $this->faker->randomElement(StudentStatus::cases()),
        ];
    }

    public function active(): self
    {
        return $this->state(fn (): array => [
            'status' => StudentStatus::ACTIVE,
        ]);
    }
}
