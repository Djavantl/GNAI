<?php

namespace App\Services\InclusiveRadar;

use App\Exceptions\BusinessRuleException;
use App\Models\InclusiveRadar\Location;
use Illuminate\Support\Facades\DB;

class LocationService
{
    public function store(array $data): Location
    {
        return DB::transaction(
            fn () => Location::create($data)
        );
    }

    public function update(Location $location, array $data): Location
    {
        return DB::transaction(function () use ($location, $data) {

            $wasActive = $location->is_active;
            $willDeactivate = $wasActive && isset($data['is_active']) && !$data['is_active'];

            if ($willDeactivate) {
                /* Bloqueamos a desativação do local para evitar que barreiras fiquem
                   "escondidas" em locais inativos no radar antes de serem resolvidas. */
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

    public function delete(Location $location): void
    {
        DB::transaction(function () use ($location) {

            /* Diferente da atualização, a exclusão física exige que o local esteja
               completamente livre de pendências para manter a integridade do mapa histórico. */
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
