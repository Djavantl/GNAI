<?php

namespace Database\Factories\SpecializedEducationalSupport;

use App\Models\SpecializedEducationalSupport\Deficiency;
use Illuminate\Database\Eloquent\Factories\Factory;

class DeficiencyFactory extends Factory
{
    protected $model = Deficiency::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement([
                'Deficiência Visual',
                'Deficiência Auditiva',
                'Deficiência Física',
                'Deficiência Intelectual',
                'Transtorno do Espectro Autista',
                'Altas Habilidades/Superdotação',
            ]),

            'cid_code' => strtoupper(
                    $this->faker->randomLetter()
                ) . $this->faker->numberBetween(10, 99),

            'description' => $this->faker->optional()->paragraph(),

            'is_active' => $this->faker->boolean(80),
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

    public function withCid(string $cid): self
    {
        return $this->state(fn () => [
            'cid_code' => strtoupper($cid),
        ]);
    }

    public function named(string $name): self
    {
        return $this->state(fn () => [
            'name' => $name,
        ]);
    }
}
