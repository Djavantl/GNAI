<?php

namespace App\Services\InclusiveRadar;

use App\Models\InclusiveRadar\ResourceStatus;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

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

    public function store(array $data): ResourceStatus
    {
        return DB::transaction(function () use ($data) {
            return ResourceStatus::create($data);
        });
    }

    public function update(ResourceStatus $status, array $data): ResourceStatus
    {
        return DB::transaction(function () use ($status, $data) {
            unset($data['code']);
            $status->update($data);
            return $status;
        });
    }

    public function toggleActive(ResourceStatus $status): ResourceStatus
    {
        return DB::transaction(function () use ($status) {
            $status->update(['is_active' => ! $status->is_active]);
            return $status;
        });
    }

    public function delete(ResourceStatus $status): void
    {
        DB::transaction(function () use ($status) {
            $status->delete();
        });
    }
}
