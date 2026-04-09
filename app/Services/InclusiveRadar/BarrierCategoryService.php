<?php

namespace App\Services\InclusiveRadar;

use App\Exceptions\BusinessRuleException;
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

    public function delete(BarrierCategory $category): void
    {
        DB::transaction(function () use ($category) {

            $hasActiveBarrier = $category
                ->barriers()
                ->get()
                ->contains(function ($barrier) {
                    $status = $barrier->latestStatus();

                    /* Assumimos que a ausência de status ou um status impeditivo impossibilita
                       a exclusão da categoria, evitando que dados históricos fiquem sem
                       classificação e dificultem auditorias futuras. */
                    if (!$status) {
                        return true;
                    }

                    return ! $status->allowsDeletion();
                });

            if ($hasActiveBarrier) {
                throw new BusinessRuleException("Esta categoria não pode ser excluída pois possui barreiras ativas.");
            }

            $category->delete();
        });
    }
}
