<?php

namespace App\Services;

use App\Models\AccessibilityFeature;
use Illuminate\Support\Facades\DB;

class AccessibilityFeatureService
{
    public function listAll()
    {
        return AccessibilityFeature::orderBy('name')->get();
    }

    public function store(array $data): AccessibilityFeature
    {
        return DB::transaction(fn () => AccessibilityFeature::create($data));
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
                'is_active' => !$feature->is_active
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
