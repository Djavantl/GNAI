<?php

namespace App\Services\InclusiveRadar;

use App\Models\SpecializedEducationalSupport\{Professional, Student};
use App\Models\InclusiveRadar\{Barrier, BarrierCategory, BarrierStatus, Institution};
use App\Services\SpecializedEducationalSupport\DeficiencyService;
use App\Enums\InclusiveRadar\InspectionType;
use Illuminate\Support\Facades\{Auth, DB};
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class BarrierService
{
    public function __construct(
        protected InspectionService $inspectionService,
        protected DeficiencyService $deficiencyService
    ) {}

    public function listAll(): Collection
    {
        return Barrier::with([
            'category', 'status', 'location', 'deficiencies',
            'inspections.images', 'registeredBy'
        ])->latest()->get();
    }

    public function getCreateData(): array
    {
        return [
            'institutions'  => Institution::with('locations')->where('is_active', true)->orderBy('name')->get(),
            'categories'    => BarrierCategory::where('is_active', true)->get(),
            'statuses'      => BarrierStatus::where('is_active', true)->get(),
            'deficiencies'  => $this->deficiencyService->listAll(),
            'students'      => Student::has('person')->with('person')->get()->sortBy('person.name'),
            'professionals' => Professional::has('person')->with('person')->get()->sortBy('person.name'),
        ];
    }

    public function getEditData(Barrier $barrier): array
    {
        return array_merge($this->getCreateData(), [
            'barrier' => $barrier->load(['deficiencies', 'inspections.images', 'location'])
        ]);
    }

    public function store(array $data): Barrier
    {
        return DB::transaction(function () use ($data) {
            if (Auth::check()) {
                $data['registered_by_user_id'] = Auth::id();
            }

            if (!empty($data['identified_at']) && str_contains($data['identified_at'], '/')) {
                $data['identified_at'] = Carbon::createFromFormat('d/m/Y', $data['identified_at'])->format('Y-m-d');
            }

            $barrierData = collect($data)->except([
                'deficiencies',
                'images',
                'no_location',
                'inspection_description'
            ])->toArray();

            $barrier = Barrier::create($barrierData);

            if (!empty($data['deficiencies'])) {
                $barrier->deficiencies()->sync($data['deficiencies']);
            }

            $this->inspectionService->createForModel($barrier, [
                'state'           => null,
                'inspection_date' => $data['identified_at'] ?? now(),
                'type'            => InspectionType::INITIAL->value,
                'description'     => $data['inspection_description'] ?? 'Registro inicial da barreira.',
                'images'          => $data['images'] ?? []
            ]);

            return $barrier;
        });
    }

    public function update(Barrier $barrier, array $data): Barrier
    {
        return DB::transaction(function () use ($barrier, $data) {

            if (!empty($data['identified_at']) && str_contains($data['identified_at'], '/')) {
                $data['identified_at'] = Carbon::createFromFormat('d/m/Y', $data['identified_at'])->format('Y-m-d');
            }
            if (!empty($data['resolved_at']) && str_contains($data['resolved_at'], '/')) {
                $data['resolved_at'] = Carbon::createFromFormat('d/m/Y', $data['resolved_at'])->format('Y-m-d');
            }

            if ($data['not_applicable'] ?? false) {
                $data['affected_student_id'] = null;
                $data['affected_professional_id'] = null;
            }

            if (!empty($data['no_location'])) {
                $data['location_id'] = null;
            }

            $oldStatus = $barrier->getOriginal('barrier_status_id');
            $newStatus = $data['barrier_status_id'] ?? $oldStatus;

            $barrier->update(collect($data)->except([
                'deficiencies',
                'images',
                'inspection_description',
                'inspection_type'
            ])->toArray());

            if (array_key_exists('deficiencies', $data)) {
                $barrier->deficiencies()->sync($data['deficiencies'] ?? []);
            }

            $hasNewImages = !empty($data['images']);
            $statusChanged = (int)$newStatus !== (int)$oldStatus;

            if ($hasNewImages || $statusChanged) {
                $this->inspectionService->createForModel($barrier, [
                    'state'           => null,
                    'inspection_date' => $data['resolved_at'] ?? now(),
                    'type'            => $data['inspection_type'] ?? ($hasNewImages ? InspectionType::PERIODIC->value : InspectionType::RESOLUTION->value),
                    'description'     => $data['inspection_description'] ?? 'Atualização de status/vistoria.',
                    'images'          => $data['images'] ?? []
                ]);
            }

            return $barrier->fresh();
        });
    }

    public function toggleActive(Barrier $barrier): Barrier
    {
        $barrier->update(['is_active' => !$barrier->is_active]);
        return $barrier;
    }

    public function delete(Barrier $barrier): void
    {
        DB::transaction(fn () => $barrier->delete());
    }
}
