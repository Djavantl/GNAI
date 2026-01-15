<?php

namespace App\Services\InclusiveRadar;

use App\Models\InclusiveRadar\BarrierCategory;
use Illuminate\Support\Facades\DB;

class BarrierCategoryService
{
    public function listAll()
    {
        return BarrierCategory::with('barriers')
            ->orderBy('name')
            ->get();
    }

    public function store(array $data): BarrierCategory
    {
        return DB::transaction(function () use ($data) {
            return BarrierCategory::create($data);
        });
    }

    public function update(BarrierCategory $category, array $data): BarrierCategory
    {
        return DB::transaction(function () use ($category, $data) {
            $category->update($data);
            return $category;
        });
    }

    public function toggleActive(BarrierCategory $category): BarrierCategory
    {
        return DB::transaction(function () use ($category) {
            $category->update(['is_active' => ! $category->is_active]);
            return $category;
        });
    }

    public function delete(BarrierCategory $category): void
    {
        DB::transaction(function () use ($category) {
            $category->delete();
        });
    }
}
