<?php

declare(strict_types=1);

namespace Database\Factories\Domains\Inclusion;

use App\Domains\Inclusion\Domain\Enums\GuardianRelationship;
use App\Domains\Inclusion\Domain\Models\Guardian;
use App\Domains\Inclusion\Domain\Models\Person;
use App\Domains\Inclusion\Domain\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

final class GuardianFactory extends Factory
{
    protected $model = Guardian::class;

    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'person_id' => Person::factory(),
            'relationship' => $this->faker->randomElement(GuardianRelationship::cases()),
        ];
    }
}
