<?php

namespace App\Services\InclusiveRadar;

use App\Models\InclusiveRadar\AccessibilityFeature;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AccessibilityFeatureService
{
    public function listAll(): Collection
    {
        return AccessibilityFeature::orderBy('name')->get();
    }

    public function store(array $data): AccessibilityFeature
    {
        return DB::transaction(function () use ($data) {
            return AccessibilityFeature::create($data);
        });
    }

    public function update(AccessibilityFeature $feature, array $data): AccessibilityFeature
    {
        return DB::transaction(function () use ($feature, $data) {
            $feature->update($data);
            return $feature;
        });
    }

    public function toggleActive(AccessibilityFeature $feature): AccessibilityFeature
    {
        return DB::transaction(function () use ($feature) {
            $feature->update([
                'is_active' => !$feature->is_active,
            ]);

            return $feature;
        });
    }

    public function delete(AccessibilityFeature $feature): void
    {
        DB::transaction(function () use ($feature) {
            $feature->delete();
        });
    }
}
