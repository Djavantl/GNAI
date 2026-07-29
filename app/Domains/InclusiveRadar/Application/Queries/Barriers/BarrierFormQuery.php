<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Queries\Barriers;

use App\Domains\InclusiveRadar\Domain\Enums\BarrierStatus;
use App\Domains\InclusiveRadar\Domain\Models\Barrier;
use App\Domains\InclusiveRadar\Domain\Models\BarrierCategory;
use App\Domains\InclusiveRadar\Domain\Models\Institution;
use App\Enums\Priority;
use App\Models\SpecializedEducationalSupport\Deficiency;
use App\Models\SpecializedEducationalSupport\Professional;
use App\Models\SpecializedEducationalSupport\Student;

final readonly class BarrierFormQuery
{
    public function forCreation(?int $selectedInstitutionId = null): array
    {
        $data = $this->baseData();

        return $data + [
            'selectedInstitution' => $selectedInstitutionId !== null
                ? $data['institutions']->firstWhere('id', $selectedInstitutionId)
                : null,
            'defaultStatus' => BarrierStatus::IDENTIFIED->value,
        ];
    }

    public function forUpdate(Barrier $barrier, ?int $selectedInstitutionId = null): array
    {
        $data = $this->baseData();

        return $data + [
            'barrier' => $barrier->loadMissing(['deficiencies', 'inspections.evidences', 'location', 'institution', 'category', 'registeredBy']),
            'selectedInstitution' => $selectedInstitutionId !== null
                ? $data['institutions']->firstWhere('id', $selectedInstitutionId)
                : ($barrier->institution ?? null),
        ];
    }

    private function baseData(): array
    {
        $institutions = Institution::query()
            ->with(['locations' => fn ($query) => $query->where('is_active', true)])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return [
            'institutions' => $institutions,
            'categories' => BarrierCategory::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),
            'deficiencies' => Deficiency::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),
            'students' => Student::query()
                ->select(['id', 'person_id'])
                ->whereHas('person')
                ->with('person:id,name')
                ->get()
                ->mapWithKeys(fn (Student $student) => [$student->id => $student->person?->name])
                ->sort(),
            'professionals' => Professional::query()
                ->select(['id', 'person_id'])
                ->whereHas('person')
                ->with('person:id,name')
                ->get()
                ->mapWithKeys(fn (Professional $professional) => [$professional->id => $professional->person?->name])
                ->sort(),
            'priorities' => collect(Priority::options()),
            'barrierStatuses' => collect(BarrierStatus::cases())
                ->mapWithKeys(fn (BarrierStatus $status) => [$status->value => $status->label()]),
        ];
    }
}
