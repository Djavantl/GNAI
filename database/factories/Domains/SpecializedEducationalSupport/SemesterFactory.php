<?php

declare(strict_types=1);

namespace Database\Factories\Domains\SpecializedEducationalSupport;

use App\Domains\SpecializedEducationalSupport\Domain\Models\Semester;
use Illuminate\Database\Eloquent\Factories\Factory;

final class SemesterFactory extends Factory
{
    protected $model = Semester::class;

    public function definition(): array
    {
        $year = $this->faker->numberBetween(2020, 2035);
        $term = $this->faker->numberBetween(1, 2);
        $startDate = $term === 1 ? "{$year}-02-01" : "{$year}-08-01";
        $endDate = $term === 1 ? "{$year}-06-30" : "{$year}-12-15";

        return [
            'year' => $year,
            'term' => $term,
            'label' => "{$year}.{$term}",
            'start_date' => $startDate,
            'end_date' => $endDate,
            'is_current' => false,
        ];
    }

    public function current(): self
    {
        return $this->state(fn (): array => [
            'is_current' => true,
        ]);
    }

    public function historical(): self
    {
        return $this->state(fn (): array => [
            'is_current' => false,
        ]);
    }
}
