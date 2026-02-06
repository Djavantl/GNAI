<?php

namespace App\Services\InclusiveRadar;

use App\Enums\InclusiveRadar\{BarrierStatus, InspectionType};
use App\Models\InclusiveRadar\{Barrier, BarrierCategory, Institution};
use App\Models\SpecializedEducationalSupport\{Professional, Student};
use App\Services\SpecializedEducationalSupport\DeficiencyService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\{Auth, DB};

class BarrierService
{
    public function __construct(
        protected InspectionService $inspectionService,
        protected DeficiencyService $deficiencyService
    ) {}

    public function listAll(): Collection
    {
        return Barrier::with([
            'category',
            'location',
            'deficiencies',
            'inspections.images',
            'registeredBy'
        ])->latest()->get();
    }

    public function getCreateData(): array
    {
        return [
            'institutions' => $this->getActiveInstitutionsWithLocationsAndBarriers(),
            'categories'    => BarrierCategory::where('is_active', true)->get(),
            'deficiencies'  => $this->deficiencyService->listActiveOrdered(),
            'students'      => Student::has('person')->with('person')->get()->sortBy('person.name'),
            'professionals' => Professional::has('person')->with('person')->get()->sortBy('person.name'),
        ];
    }

    public function getEditData(Barrier $barrier): array
    {
        return array_merge($this->getCreateData(), [
            'barrier' => $barrier->load(['deficiencies', 'inspections.images', 'location', 'institution',])
        ]);
    }

    public function store(array $data): Barrier
    {
        return DB::transaction(fn() => $this->persist(new Barrier(), $data));
    }

    public function update(Barrier $barrier, array $data): Barrier
    {
        return DB::transaction(fn() => $this->persist($barrier, $data));
    }

    public function toggleActive(Barrier $barrier): Barrier
    {
        $barrier->update(['is_active' => !$barrier->is_active]);
        return $barrier->fresh();
    }

    public function delete(Barrier $barrier): void
    {
        DB::transaction(fn() => $barrier->delete());
    }

    protected function persist(Barrier $barrier, array $data): Barrier
    {
        $data = $this->sanitizeReporterData($data);
        $data = $this->prepareData($barrier, $data);
        $barrier->fill($data)->save();

        $this->syncRelations($barrier, $data);
        $this->handleInspectionLog($barrier, $data);

        return $barrier->fresh(['category', 'location', 'deficiencies']);
    }

    protected function prepareData(Barrier $barrier, array $data): array
    {
        if (!$barrier->exists && Auth::check()) {
            $data['registered_by_user_id'] = Auth::id();
        }

        if (!empty($data['no_location'])) {
            $data['location_id'] = null;
        }

        return $data;
    }

    protected function sanitizeReporterData(array $data): array
    {

        $allReporterFields = [
            'affected_student_id'      => null,
            'affected_professional_id' => null,
            'affected_person_name'     => null,
            'affected_person_role'     => null,
            'is_anonymous'             => false,
            'not_applicable'           => false,
        ];


        if (!empty($data['is_anonymous'])) {
            return array_merge($data, $allReporterFields, ['is_anonymous' => true]);
        }

        if (!empty($data['not_applicable'])) {
            return array_merge($data, $allReporterFields, [
                'not_applicable'       => true,
                'affected_person_name' => $data['affected_person_name'] ?? null,
                'affected_person_role' => $data['affected_person_role'] ?? null,
            ]);
        }

        return array_merge($data, [
            'is_anonymous'         => false,
            'not_applicable'       => false,
            'affected_person_name' => $data['affected_person_name'] ?? null,
            'affected_person_role' => $data['affected_person_role'] ?? null,
        ]);
    }

    protected function handleInspectionLog(Barrier $barrier, array $data): void
    {
        $isUpdate = $barrier->wasRecentlyCreated === false;
        $oldStatus = $isUpdate ? $barrier->latestStatus()?->value : null;
        $newStatus = $data['status'] ?? $oldStatus ?? BarrierStatus::IDENTIFIED->value;

        $statusChanged = $isUpdate && $newStatus !== $oldStatus;
        $hasInteraction = filled($data['inspection_description'] ?? null) || !empty($data['images']);

        $finalStatuses = [
            BarrierStatus::RESOLVED->value,
            BarrierStatus::NOT_APPLICABLE->value
        ];

        if (in_array($newStatus, $finalStatuses)) {
            $barrier->update(['resolved_at' => $barrier->resolved_at ?? now()]);
        } else {
            $barrier->update(['resolved_at' => null]);
        }

        if ($isUpdate && !$statusChanged && !$hasInteraction) {
            return;
        }

        $this->inspectionService->createForModel($barrier, [
            'state'           => null,
            'status'          => $newStatus,
            'inspection_date' => $data['inspection_date'] ?? now(),
            'type'            => $data['inspection_type'] ?? ($isUpdate ? InspectionType::PERIODIC->value : InspectionType::INITIAL->value),
            'description'     => $data['inspection_description'] ?? ($isUpdate ? null : 'Registro inicial da barreira.'),
            'images'          => $data['images'] ?? [],
        ]);
    }

    protected function syncRelations(Barrier $barrier, array $data): void
    {
        if (isset($data['deficiencies'])) {
            $barrier->deficiencies()->sync($data['deficiencies']);
        }
    }

    public function getActiveInstitutionsWithLocationsAndBarriers()
    {
        return Institution::with([
            'locations' => function ($q) {
                $q->where('is_active', true);
            },
            'barriers' => function ($q) {
                $q->where('is_active', true)
                    ->with(['deficiencies', 'inspections' => function ($query) {
                        $query->latest()->limit(1);
                    }]);
            }
        ])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }
}
