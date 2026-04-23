<?php

namespace Database\Factories\InclusiveRadar;

use App\Enums\InclusiveRadar\LoanStatus;
use App\Models\InclusiveRadar\AccessibleEducationalMaterial;
use App\Models\InclusiveRadar\AssistiveTechnology;
use App\Models\InclusiveRadar\Loan;
use App\Models\SpecializedEducationalSupport\Professional;
use App\Models\SpecializedEducationalSupport\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoanFactory extends Factory
{
    protected $model = Loan::class;

    public function configure(): static
    {
        return $this->forAssistiveTechnology()->forStudent();
    }

    public function definition(): array
    {
        $loanDate = $this->faker->dateTimeBetween('-30 days', 'now');
        $dueDate = (clone $loanDate)->modify('+' . $this->faker->numberBetween(1, 15) . ' days');

        return [
            'loanable_id' => null,
            'loanable_type' => null,
            'student_id' => null,
            'professional_id' => null,
            'user_id' => User::factory(),
            'loan_date' => $loanDate,
            'due_date' => $dueDate,
            'return_date' => null,
            'status' => LoanStatus::ACTIVE,
            'observation' => $this->faker->optional()->sentence(),
        ];
    }

    public function forAssistiveTechnology(?AssistiveTechnology $assistiveTechnology = null): self
    {
        return $this->for(
            $assistiveTechnology ?? AssistiveTechnology::factory()->physical()->available(),
            'loanable'
        )
            ->state(fn () => [
                'loanable_type' => (new AssistiveTechnology())->getMorphClass(),
            ]);
    }

    public function forAccessibleEducationalMaterial(?AccessibleEducationalMaterial $material = null): self
    {
        return $this->for(
            $material ?? AccessibleEducationalMaterial::factory()->physical()->available(),
            'loanable'
        )
            ->state(fn () => [
                'loanable_type' => (new AccessibleEducationalMaterial())->getMorphClass(),
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

    public function returned(): self
    {
        return $this->state(function (array $attributes) {
            $dueDate = $attributes['due_date'];
            $returnDate = $dueDate instanceof \DateTimeInterface
                ? (clone $dueDate)->modify('-1 day')
                : now()->subDay();

            return [
                'status' => LoanStatus::RETURNED,
                'return_date' => $returnDate,
            ];
        });
    }

    public function late(): self
    {
        return $this->state(function (array $attributes) {
            $dueDate = $attributes['due_date'];
            $returnDate = $dueDate instanceof \DateTimeInterface
                ? (clone $dueDate)->modify('+2 days')
                : now()->addDays(2);

            return [
                'status' => LoanStatus::LATE,
                'return_date' => $returnDate,
            ];
        });
    }

    public function damaged(): self
    {
        return $this->state(function (array $attributes) {
            $dueDate = $attributes['due_date'];
            $returnDate = $dueDate instanceof \DateTimeInterface
                ? (clone $dueDate)->modify('+1 day')
                : now()->addDay();

            return [
                'status' => LoanStatus::DAMAGED,
                'return_date' => $returnDate,
            ];
        });
    }
}
