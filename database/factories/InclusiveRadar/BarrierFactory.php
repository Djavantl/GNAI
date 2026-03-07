<?php

namespace Database\Factories\InclusiveRadar;

use App\Enums\Priority;
use App\Models\InclusiveRadar\Barrier;
use App\Models\InclusiveRadar\BarrierCategory;
use App\Models\InclusiveRadar\Institution;
use App\Models\InclusiveRadar\Location;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BarrierFactory extends Factory
{
    protected $model = Barrier::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph,
            'registered_by_user_id' => User::factory(),
            'institution_id' => Institution::factory(),
            'barrier_category_id' => BarrierCategory::factory(),
            'location_id' => Location::factory(),

            // Campos opcionais (nulos por padrão ou com lógica Faker)
            'affected_student_id' => null,
            'affected_professional_id' => null,
            'not_applicable' => false,
            'is_anonymous' => false,
            'affected_person_name' => null,
            'affected_person_role' => null,

            'priority' => $this->faker->randomElement(Priority::cases()),
            'identified_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'resolved_at' => null,
            'is_active' => true,
            'latitude' => $this->faker->latitude,
            'longitude' => $this->faker->longitude,
            'location_specific_details' => $this->faker->sentence,
        ];
    }

    /**
     * Estado para uma barreira anônima.
     */
    public function anonymous(): self
    {
        return $this->state(fn (array $attributes) => [
            'is_anonymous' => true,
            'registered_by_user_id' => null,
        ]);
    }

    /**
     * Estado para uma barreira resolvida.
     */
    public function resolved(): self
    {
        return $this->state(fn (array $attributes) => [
            'resolved_at' => now(),
            'is_active' => false,
        ]);
    }

    /**
     * Estado para relato geral (not_applicable).
     */
    public function generalReport(): self
    {
        return $this->state(fn (array $attributes) => [
            'not_applicable' => true,
            'affected_person_name' => $this->faker->name,
            'affected_person_role' => 'Visitante',
        ]);
    }
}
