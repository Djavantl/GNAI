<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Queries\Barriers;

use App\Domains\InclusiveRadar\Domain\Enums\BarrierStatus;
use App\Domains\InclusiveRadar\Domain\Enums\Priority;
use App\Domains\InclusiveRadar\Domain\Models\Barrier;
use App\Domains\InclusiveRadar\Domain\Models\BarrierCategory;
use App\Domains\InclusiveRadar\Domain\Models\Institution;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Deficiency;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Professional;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;

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
        $data = $this->baseData($barrier);

        return $data + [
            'barrier' => $barrier->loadMissing(['deficiencies', 'inspections.evidences', 'location', 'institution', 'category', 'registeredBy']),
            'selectedInstitution' => $selectedInstitutionId !== null
                ? $data['institutions']->firstWhere('id', $selectedInstitutionId)
                : $data['institutions']->firstWhere('id', $barrier->institution_id),
        ];
    }

    private function baseData(?Barrier $barrier = null): array
    {
        $institutions = Institution::query()
            ->with(['locations' => function ($query) use ($barrier): void {
                $query->where('is_active', true);
                if ($barrier?->location_id !== null) {
                    $query->orWhere('id', $barrier->location_id);
                }
            }])
            ->where(function ($query) use ($barrier): void {
                $query->where('is_active', true);
                if ($barrier?->institution_id !== null) {
                    $query->orWhere('id', $barrier->institution_id);
                }
            })
            ->orderBy('name')
            ->get();

        $categories = BarrierCategory::query()
            ->where(function ($query) use ($barrier): void {
                $query->where('is_active', true);
                if ($barrier?->barrier_category_id !== null) {
                    $query->orWhere('id', $barrier->barrier_category_id);
                }
            })
            ->orderBy('name')
            ->get();

        $deficiencies = Deficiency::query()
            ->where(function ($query) use ($barrier): void {
                $query->where('is_active', true);
                if ($barrier !== null) {
                    $query->orWhereIn('id', $barrier->deficiencies()->pluck('deficiencies.id'));
                }
            })
            ->orderBy('name')
            ->get();

        $institutionData = $institutions->map(fn (Institution $institution): array => [
            'id' => $institution->id,
            'name' => $institution->name,
            'latitude' => (float) $institution->latitude,
            'longitude' => (float) $institution->longitude,
            'default_zoom' => $institution->default_zoom ?? Institution::DEFAULT_ZOOM,
            'locations' => $institution->locations->map(fn ($location): array => [
                'id' => $location->id,
                'name' => $location->name,
                'label' => $location->name,
                'latitude' => (float) $location->latitude,
                'longitude' => (float) $location->longitude,
            ])->values(),
        ])->values();

        return [
            'institutions' => $institutionData,
            'categories' => $categories->map(fn (BarrierCategory $category): array => [
                'id' => $category->id,
                'name' => $category->name,
                'blocks_map' => $category->blocks_map,
            ])->values(),
            'deficiencies' => $deficiencies->map(fn (Deficiency $deficiency): array => [
                'id' => $deficiency->id,
                'name' => $deficiency->name,
            ])->values(),
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
            'priorities' => collect(Priority::options())
                ->map(fn (string $label, string $value): array => ['value' => $value, 'label' => $label])
                ->values(),
            'barrierStatuses' => collect(BarrierStatus::cases())
                ->map(fn (BarrierStatus $status): array => ['value' => $status->value, 'label' => $status->label()])
                ->values(),
        ];
    }
}
