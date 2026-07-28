<?php

declare(strict_types=1);

namespace App\Domains\Dashboard\Application\Queries;

use App\Domains\SpecializedEducationalSupport\Domain\Enums\SessionStatus;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Course;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Pei;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Pendency;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Professional;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Session;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;
use App\Enums\Priority;
use Illuminate\Support\Collection;

final readonly class SpecializedEducationalSupportDashboardQuery
{
    /**
     * @return array<string, mixed>
     */
    public function execute(): array
    {
        return [
            'totalStudents' => $this->totalStudents(),
            'totalSessions' => $this->totalSessions(),
            'totalPeis' => $this->totalPeis(),
            'totalProfessionals' => $this->totalProfessionals(),
            'totalCourses' => $this->totalCourses(),
            'totalPeisFinished' => $this->totalFinishedPeis(),
            'totalPeisNotFinished' => $this->totalUnfinishedPeis(),
            'totalPendingPendencies' => $this->totalPendingPendencies(),
            'totalOverduePendencies' => $this->totalOverduePendencies(),
            'pendenciesByPriority' => $this->pendenciesByPriority(),
            'sessionsByStatus' => $this->sessionsByStatus(),
        ];
    }

    private function totalStudents(): int
    {
        return Student::count();
    }

    private function totalSessions(): int
    {
        return Session::count();
    }

    private function totalPeis(): int
    {
        return Pei::count();
    }

    private function totalProfessionals(): int
    {
        return Professional::count();
    }

    private function totalCourses(): int
    {
        return Course::count();
    }

    private function totalFinishedPeis(): int
    {
        return Pei::where('is_finished', true)->count();
    }

    private function totalUnfinishedPeis(): int
    {
        return Pei::where('is_finished', false)->count();
    }

    private function totalPendingPendencies(): int
    {
        return Pendency::query()
            ->where('is_completed', false)
            ->count();
    }

    private function totalOverduePendencies(): int
    {
        return Pendency::query()
            ->where('is_completed', false)
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', today())
            ->count();
    }

    /**
     * @return Collection<int, array{label: string, color: string, count: int}>
     */
    private function pendenciesByPriority(): Collection
    {
        return collect(Priority::cases())
            ->map(fn (Priority $priority): array => [
                'label' => $priority->label(),
                'color' => $priority->color(),
                'count' => Pendency::query()
                    ->where('is_completed', false)
                    ->where('priority', $priority->value)
                    ->count(),
            ])
            ->values();
    }

    /**
     * @return Collection<int, array{label: string, count: int}>
     */
    private function sessionsByStatus(): Collection
    {
        return collect(SessionStatus::options())
            ->map(fn (string $label, string $status): array => [
                'label' => $label,
                'count' => Session::where('status', $status)->count(),
            ])
            ->values();
    }
}
