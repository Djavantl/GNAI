<?php

namespace Database\Factories\Domains\InclusiveRadar;

use App\Domains\InclusiveRadar\Domain\Models\Institution;
use App\Domains\InclusiveRadar\Domain\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

class LocationFactory extends Factory
{
    protected $model = Location::class;

    public function definition(): array
    {
        return [
            'institution_id' => Institution::factory(),
            'name' => $this->faker->words(3, true),
            'type' => $this->faker->randomElement(['Bloco', 'Laboratório', 'Auditório', 'Estacionamento']),
            'description' => $this->faker->sentence(),
            'latitude' => $this->faker->latitude(-14.25, -14.20),
            'longitude' => $this->faker->longitude(-42.80, -42.70),
            'google_place_id' => $this->faker->optional()->uuid(),
            'is_active' => true,
        ];
    }

    public function active(): self
    {
        return $this->state(fn () => [
            'is_active' => true,
        ]);
    }

    public function inactive(): self
    {
        return $this->state(fn () => [
            'is_active' => false,
        ]);
    }
}
