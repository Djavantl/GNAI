<?php

namespace App\Services\InclusiveRadar;

use App\Exceptions\BusinessRuleException;
use App\Models\InclusiveRadar\Location;
use Illuminate\Support\Facades\DB;

class LocationService
{
    /**
     * RF: cria um ponto de referência vinculado à instituição.
     * Uso: cadastro de locais usados por barreiras e mapa do radar.
     */
    public function store(array $data): Location
    {
        return DB::transaction(
            fn () => Location::create($data)
        );
    }

    /**
     * RF: atualiza o local impedindo desativação com barreiras pendentes.
     * Uso: manutenção de pontos de referência sem esconder ocorrências ativas.
     */
    public function update(Location $location, array $data): Location
    {
        return DB::transaction(function () use ($location, $data) {

            $wasActive = $location->is_active;
            $willDeactivate = $wasActive && isset($data['is_active']) && !$data['is_active'];

            if ($willDeactivate) {
                $hasUnresolvedBarriers = $location
                    ->barriers()
                    ->whereNull('resolved_at')
                    ->exists();

                if ($hasUnresolvedBarriers) {
                    throw new BusinessRuleException('Existem barreiras não resolvidas vinculadas a este local. Resolva-as antes de desativá-lo.');
                }
            }

            $location->update($data);

            return $location;
        });
    }

    /**
     * RF: exclui o local somente quando não houver barreiras ativas vinculadas.
     * Uso: limpeza administrativa segura de pontos de referência.
     */
    public function delete(Location $location): void
    {
        DB::transaction(function () use ($location) {
            $hasActiveBarriers = $location
                ->barriers()
                ->whereNull('resolved_at')
                ->exists();

            if ($hasActiveBarriers) {
                throw new BusinessRuleException("Não é possível excluir este ponto de referência pois ele possui barreiras ativas.");
            }

            $location->delete();
        });
    }
}
