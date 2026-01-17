<?php

namespace App\Services\InclusiveRadar;

use App\Models\InclusiveRadar\ResourceStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

class ResourceStatusService
{

    public function listAll(): Collection
    {
        return ResourceStatus::orderBy('name')->get();
    }

    public function listForAssistiveTechnology(): Collection
    {
        return ResourceStatus::active()
            ->forAssistiveTechnology()
            ->orderBy('name')
            ->get();
    }

    public function listForEducationalMaterial(): Collection
    {
        return ResourceStatus::active()
            ->forEducationalMaterial()
            ->orderBy('name')
            ->get();
    }

    public function update(ResourceStatus $status, array $data): ResourceStatus
    {
        unset($data['code']);

        $status->update($data);

        return $status;
    }

    public function toggleActive(ResourceStatus $status): ResourceStatus
    {
        $status->update([
            'is_active' => ! $status->is_active
        ]);

        return $status;
    }
}
