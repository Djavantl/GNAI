<?php

namespace Database\Factories\InclusiveRadar;

use App\Models\InclusiveRadar\AccessibilityFeature;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccessibilityFeatureFactory extends Factory
{
    protected $model = AccessibilityFeature::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement([
                'Audiodescrição',
                'Legenda Oculta',
                'Libras',
                'Alto Contraste',
                'Fonte Ampliada',
                'Leitor de Tela Compatível',
                'Navegação por Teclado',
                'Material Tátil',
            ]),

            'description' => $this->faker->optional()->sentence(),
            'is_active' => $this->faker->boolean(85),
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

    public function named(string $name): self
    {
        return $this->state(fn () => [
            'name' => $name,
        ]);
    }
}
