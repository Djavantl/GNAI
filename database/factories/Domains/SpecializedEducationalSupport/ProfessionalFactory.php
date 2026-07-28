<?php

declare(strict_types=1);

namespace Database\Factories\Domains\SpecializedEducationalSupport;

use App\Domains\SpecializedEducationalSupport\Domain\Enums\ProfessionalStatus;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Person;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Position;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Professional;
use Illuminate\Database\Eloquent\Factories\Factory;

final class ProfessionalFactory extends Factory
{
    protected $model = Professional::class;

    public function definition(): array
    {
        return [
            'person_id' => Person::factory(),
            'position_id' => Position::factory(),
            'registration' => $this->faker->unique()->bothify('PROF######'),
            'entry_date' => $this->faker->dateTimeBetween('-10 years', 'now')->format('Y-m-d'),
            'status' => $this->faker->randomElement(ProfessionalStatus::cases()),
        ];
    }

    public function active(): self
    {
        return $this->state(fn (): array => [
            'status' => ProfessionalStatus::ACTIVE,
        ]);
    }
}
