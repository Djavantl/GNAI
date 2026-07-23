<?php

namespace Database\Factories\Domains\InclusiveRadar;

use App\Domains\InclusiveRadar\Domain\Models\Barrier;
use App\Domains\InclusiveRadar\Domain\Models\BarrierCategory;
use App\Domains\InclusiveRadar\Domain\Models\Institution;
use App\Enums\Priority;
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
            'location_id' => null,
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

    public function anonymous(): self
    {
        return $this->state(fn () => [
            'is_anonymous' => true,
            'not_applicable' => false,
            'registered_by_user_id' => null,
            'affected_student_id' => null,
            'affected_professional_id' => null,
            'affected_person_name' => null,
            'affected_person_role' => null,
        ]);
    }

    public function resolved(): self
    {
        return $this->state(fn () => [
            'resolved_at' => now(),
            'is_active' => false,
        ]);
    }

    public function generalReport(): self
    {
        return $this->state(fn () => [
            'not_applicable' => true,
            'is_anonymous' => false,
            'affected_student_id' => null,
            'affected_professional_id' => null,
            'affected_person_name' => $this->faker->name,
            'affected_person_role' => 'Visitante',
        ]);
    }
}
