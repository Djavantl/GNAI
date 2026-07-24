<?php

namespace Database\Factories\Domains\InclusiveRadar;

use App\Domains\InclusiveRadar\Domain\Enums\WaitlistStatus;
use App\Domains\InclusiveRadar\Domain\Models\AccessibleEducationalMaterial;
use App\Domains\InclusiveRadar\Domain\Models\AssistiveTechnology;
use App\Domains\InclusiveRadar\Domain\Models\Waitlist;
use App\Models\SpecializedEducationalSupport\Professional;
use App\Models\SpecializedEducationalSupport\Student;
use App\Domains\Auth\Domain\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class WaitlistFactory extends Factory
{
    protected $model = Waitlist::class;

    public function configure(): static
    {
        return $this->forAssistiveTechnology()->forStudent();
    }

    public function definition(): array
    {
        return [
            'waitlistable_id' => null,
            'waitlistable_type' => null,
            'student_id' => null,
            'professional_id' => null,
            'user_id' => User::factory(),
            'requested_at' => $this->faker->dateTimeBetween('-15 days', 'now'),
            'status' => WaitlistStatus::WAITING,
            'observation' => $this->faker->optional()->sentence(),
        ];
    }

    public function forAssistiveTechnology(?AssistiveTechnology $assistiveTechnology = null): self
    {
        return $this->for(
            $assistiveTechnology ?? AssistiveTechnology::factory()->physical()->unavailable(),
            'waitlistable'
        )->state(fn () => [
            'waitlistable_type' => (new AssistiveTechnology())->getMorphClass(),
        ]);
    }

    public function forAccessibleEducationalMaterial(?AccessibleEducationalMaterial $material = null): self
    {
        return $this->for(
            $material ?? AccessibleEducationalMaterial::factory()->physical()->unavailable(),
            'waitlistable'
        )->state(fn () => [
            'waitlistable_type' => (new AccessibleEducationalMaterial())->getMorphClass(),
        ]);
    }

    public function forStudent(?Student $student = null): self
    {
        return $this->state(fn () => [
            'student_id' => $student?->id ?? Student::factory(),
            'professional_id' => null,
        ]);
    }

    public function forProfessional(?Professional $professional = null): self
    {
        return $this->state(fn () => [
            'student_id' => null,
            'professional_id' => $professional?->id ?? Professional::factory(),
        ]);
    }

    public function notified(): self
    {
        return $this->state(fn () => [
            'status' => WaitlistStatus::NOTIFIED,
        ]);
    }

    public function fulfilled(): self
    {
        return $this->state(fn () => [
            'status' => WaitlistStatus::FULFILLED,
        ]);
    }

    public function cancelled(): self
    {
        return $this->state(fn () => [
            'status' => WaitlistStatus::CANCELLED,
        ]);
    }
}
