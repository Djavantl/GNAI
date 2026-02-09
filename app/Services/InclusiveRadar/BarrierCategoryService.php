<?php

namespace App\Services\InclusiveRadar;

use App\Enums\InclusiveRadar\BarrierStatus;
use App\Exceptions\InclusiveRadar\CannotDeleteLinkedBarrierException;
use App\Models\InclusiveRadar\BarrierCategory;
use Illuminate\Support\Facades\DB;

class BarrierCategoryService
{
    public function store(array $data): BarrierCategory
    {
        return DB::transaction(
            fn () => BarrierCategory::create($data)
        );
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

            $category->update([
                'is_active' => !$category->is_active
            ]);

            return $category;
        });
    }

    public function delete(BarrierCategory $category): void
    {
        DB::transaction(function () use ($category) {

            $hasActiveBarrier = $category
                ->barriers()
                ->get()
                ->contains(function ($barrier) {

                    $status = $barrier->latestStatus();

                    // Sem status → considera ativa
                    if (!$status) {
                        return true;
                    }

                    return ! $status->allowsDeletion();
                });

            if ($hasActiveBarrier) {
                throw new CannotDeleteLinkedBarrierException();
            }

            $category->delete();
        });
    }
}
