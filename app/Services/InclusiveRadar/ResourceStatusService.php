<?php

namespace App\Services\InclusiveRadar;

use App\Models\InclusiveRadar\ResourceStatus;
use Illuminate\Support\Facades\DB;

class ResourceStatusService
{
    public function store(array $data): ResourceStatus
    {
        return DB::transaction(function () use ($data) {
            return ResourceStatus::create($data);
        });
    }

    public function update(ResourceStatus $resourceStatus, array $data): ResourceStatus
    {
        return DB::transaction(function () use ($resourceStatus, $data) {
            unset($data['code']);
            $resourceStatus->update($data);
            return $resourceStatus;
        });
    }

    public function toggleActive(ResourceStatus $resourceStatus): ResourceStatus
    {
        return DB::transaction(function () use ($resourceStatus) {
            $resourceStatus->update([
                'is_active' => ! $resourceStatus->is_active
            ]);

            return $resourceStatus;
        });
    }

    public function delete(ResourceStatus $resourceStatus): void
    {
        DB::transaction(fn() => $resourceStatus->delete());
    }
}
