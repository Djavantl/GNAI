<?php

namespace Database\Factories\Domains\InclusiveRadar;

use App\Domains\InclusiveRadar\Domain\Models\Inspection;
use App\Domains\InclusiveRadar\Domain\Models\InspectionEvidence;
use Illuminate\Database\Eloquent\Factories\Factory;

class InspectionEvidenceFactory extends Factory
{
    protected $model = InspectionEvidence::class;

    public function definition(): array
    {
        $extension = $this->faker->randomElement(['jpg', 'jpeg', 'png', 'webp']);

        return [
            'inspection_id' => Inspection::factory(),
            'path' => 'inspections/' . $this->faker->uuid() . '.' . $extension,
            'original_name' => 'evidencia-' . $this->faker->numerify('###') . '.' . $extension,
            'mime_type' => match ($extension) {
                'png' => 'image/png',
                'webp' => 'image/webp',
                default => 'image/jpeg',
            },
            'size' => $this->faker->numberBetween(50_000, 2_000_000),
        ];
    }
}
