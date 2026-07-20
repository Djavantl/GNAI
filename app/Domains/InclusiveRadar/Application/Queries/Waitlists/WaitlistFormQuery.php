<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Queries\Waitlists;

use App\Domains\InclusiveRadar\Domain\Models\AccessibleEducationalMaterial;
use App\Domains\InclusiveRadar\Domain\Models\AssistiveTechnology;
use App\Domains\InclusiveRadar\Domain\Models\Waitlist;
use App\Models\SpecializedEducationalSupport\Professional;
use App\Models\SpecializedEducationalSupport\Student;
use Illuminate\Support\Collection;

final class WaitlistFormQuery
{
    /**
     * @return array<string, mixed>
     */
    public function forCreation(): array
    {
        return $this->baseOptions() + [
            'assistive_technologies' => $this->waitlistableItems(AssistiveTechnology::class),
            'educational_materials' => $this->waitlistableItems(AccessibleEducationalMaterial::class),
            'authUser' => auth()->user(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function forUpdate(Waitlist $waitlist): array
    {
        $waitlist->loadMissing([
            'waitlistable',
            'student.person',
            'professional.person',
            'user',
        ]);

        return $this->baseOptions() + [
            'waitlist' => $waitlist,
            'statusLabel' => $waitlist->status->label(),
            'authUser' => auth()->user(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function baseOptions(): array
    {
        return [
            'students' => Student::query()
                ->with('person:id,name')
                ->get(['id', 'person_id', 'registration'])
                ->sortBy('person.name')
                ->mapWithKeys(
                    static fn (Student $student): array => [
                        $student->id => trim(
                            ($student->person?->name ?? 'Sem nome')
                            .' ('.$student->registration.')',
                        ),
                    ],
                ),
            'professionals' => Professional::query()
                ->with('person:id,name')
                ->get(['id', 'person_id'])
                ->sortBy('person.name')
                ->mapWithKeys(
                    static fn (Professional $professional): array => [
                        $professional->id => $professional->person?->name ?? 'Sem nome',
                    ],
                ),
        ];
    }

    /**
     * @param  class-string<AccessibleEducationalMaterial|AssistiveTechnology>  $model
     * @return Collection<int, AccessibleEducationalMaterial|AssistiveTechnology>
     */
    private function waitlistableItems(string $model): Collection
    {
        return $model::query()
            ->where('is_active', true)
            ->where('is_loanable', true)
            ->orderBy('name')
            ->get()
            ->filter(
                static fn (AccessibleEducationalMaterial|AssistiveTechnology $item): bool => (
                    $item->quantity_available <= 0 || $item->status->blocksLoan()
                ),
            )
            ->values();
    }
}
