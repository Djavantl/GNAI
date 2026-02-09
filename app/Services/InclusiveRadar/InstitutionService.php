<?php

namespace App\Services\InclusiveRadar;

use App\Exceptions\InclusiveRadar\CannotDeleteLinkedBarrierException;
use App\Models\InclusiveRadar\Institution;
use Illuminate\Support\Facades\DB;

class InstitutionService
{
    public function store(array $data): Institution
    {
        return DB::transaction(
            fn () => Institution::create($data)
        );
    }

    public function update(Institution $institution, array $data): Institution
    {
        return DB::transaction(function () use ($institution, $data) {
            $institution->update($data);
            return $institution;
        });
    }

    public function toggleActive(Institution $institution): Institution
    {
        return DB::transaction(function () use ($institution) {

            $institution->update([
                'is_active' => ! $institution->is_active
            ]);

            return $institution;
        });
    }

    public function delete(Institution $institution): void
    {
        DB::transaction(function () use ($institution) {

            $hasActiveBarrier = $institution
                ->barriers()
                ->get()
                ->contains(function ($barrier) {

                    $status = $barrier->latestStatus();

                    if (!$status) {
                        return true;
                    }

                    return ! $status->allowsDeletion();
                });

            if ($hasActiveBarrier) {
                throw new CannotDeleteLinkedBarrierException();
            }

            $institution->delete();
        });
    }
}
