<?php

namespace App\Services\InclusiveRadar;

use App\Models\InclusiveRadar\AccessibleEducationalMaterialStatus;
use Illuminate\Support\Facades\DB;

class AccessibleEducationalMaterialStatusService
{
    public function listAll()
    {
        return AccessibleEducationalMaterialStatus::orderBy('name')->get();
    }

    public function store(array $data): AccessibleEducationalMaterialStatus
    {
        return DB::transaction(fn () =>
        AccessibleEducationalMaterialStatus::create($data)
        );
    }

    public function update(
        AccessibleEducationalMaterialStatus $status,
        array $data
    ): AccessibleEducationalMaterialStatus {
        return DB::transaction(function () use ($status, $data) {
            $status->update($data);
            return $status;
        });
    }

    public function toggleActive(
        AccessibleEducationalMaterialStatus $status
    ): AccessibleEducationalMaterialStatus {
        return DB::transaction(function () use ($status) {
            $status->update([
                'is_active' => ! $status->is_active
            ]);

            return $status;
        });
    }

    public function delete(
        AccessibleEducationalMaterialStatus $status
    ): void {
        DB::transaction(function () use ($status) {
            $status->delete();
        });
    }
}
