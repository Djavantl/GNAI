<?php

namespace Database\Factories\InclusiveRadar;

use App\Models\InclusiveRadar\Institution;
use Illuminate\Database\Eloquent\Factories\Factory;

class InstitutionFactory extends Factory
{
    protected $model = Institution::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'short_name' => strtoupper($this->faker->lexify('???')),
            'city' => $this->faker->city,
            'state' => $this->faker->stateAbbr,
            'district' => $this->faker->optional()->citySuffix(),
            'address' => $this->faker->optional()->streetAddress(),
            'latitude' => $this->faker->latitude,
            'longitude' => $this->faker->longitude,
            'default_zoom' => 16,
            'is_active' => true,
        ];
    }

    public function inactive(): self
    {
        return $this->state(fn () => [
            'is_active' => false,
        ]);
    }
}
