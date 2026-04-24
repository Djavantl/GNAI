<?php

namespace Database\Factories\InclusiveRadar;

use App\Models\InclusiveRadar\InstitutionalEvent;
use Illuminate\Database\Eloquent\Factories\Factory;

class InstitutionalEventFactory extends Factory
{
    protected $model = InstitutionalEvent::class;

    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-2 months', '+3 months');
        $endDate = $this->faker->boolean(75)
            ? (clone $startDate)->modify('+' . $this->faker->numberBetween(0, 2) . ' days')
            : null;
        $startTime = $this->faker->boolean(80) ? $this->faker->time('H:i:s') : null;
        $endTime = $startTime
            ? date('H:i:s', strtotime($startTime . ' +' . $this->faker->numberBetween(1, 4) . ' hours'))
            : null;

        return [
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->optional()->paragraph(),
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate?->format('Y-m-d'),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'location' => $this->faker->optional()->randomElement([
                'Auditório Central',
                'Biblioteca',
                'Sala Multiuso',
                'Campus ' . $this->faker->city(),
            ]),
            'organizer' => $this->faker->optional()->company(),
            'audience' => $this->faker->randomElement([
                'Estudantes',
                'Profissionais',
                'Comunidade Acadêmica',
                'Público Geral',
            ]),
            'is_active' => $this->faker->boolean(90),
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
