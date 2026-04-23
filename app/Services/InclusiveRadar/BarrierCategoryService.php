<?php

namespace App\Services\InclusiveRadar;

use App\Exceptions\BusinessRuleException;
use App\Models\InclusiveRadar\BarrierCategory;
use Illuminate\Support\Facades\DB;

class BarrierCategoryService
{
    /**
     * RF: cria uma categoria de barreira em transação única.
     * Uso: cadastro administrativo das classificações usadas no mapa.
     */
    public function store(array $data): BarrierCategory
    {
        return DB::transaction(
            fn () => BarrierCategory::create($data)
        );
    }

    /**
     * RF: atualiza a categoria de barreira mantendo consistência transacional.
     * Uso: edição das classificações exibidas em formulários e relatórios.
     */
    public function update(BarrierCategory $category, array $data): BarrierCategory
    {
        return DB::transaction(function () use ($category, $data) {
            $category->update($data);
            return $category;
        });
    }

    /**
     * RF: remove a categoria apenas quando nenhuma barreira ativa depende dela.
     * Uso: exclusão administrativa sem quebrar histórico e auditoria do mapa.
     */
    public function delete(BarrierCategory $category): void
    {
        DB::transaction(function () use ($category) {

            $hasActiveBarrier = $category
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
                throw new BusinessRuleException("Esta categoria não pode ser excluída pois possui barreiras ativas.");
            }

            $category->delete();
        });
    }
}
