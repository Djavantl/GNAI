<?php

namespace App\Services\InclusiveRadar;

use App\Exceptions\BusinessRuleException;
use App\Models\InclusiveRadar\Institution;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InstitutionService
{
    /**
     * RF: cria a instituição única da instância do sistema.
     * Uso: bootstrap do radar e manutenção do cadastro institucional principal.
     */
    public function store(array $data): ?Institution
    {
        if (Institution::exists()) {
            return null;
        }

        return DB::transaction(fn () => Institution::create($data));
    }

    /**
     * RF: atualiza a instituição e impede desativação com pendências abertas.
     * Uso: manutenção do cadastro base que sustenta locais e barreiras.
     */
    public function update(Institution $institution, array $data): Institution
    {
        return DB::transaction(function () use ($institution, $data) {

            $wasActive = $institution->is_active;
            $willDeactivate = $wasActive && isset($data['is_active']) && !$data['is_active'];

            if ($willDeactivate) {
                $hasUnresolvedBarriers = $institution
                    ->barriers()
                    ->whereNull('resolved_at')
                    ->exists();

                if ($hasUnresolvedBarriers) {
                    throw ValidationException::withMessages([
                        'is_active' => 'Existem barreiras não resolvidas. Resolva-as antes de desativar a instituição.'
                    ]);
                }
            }

            $institution->update($data);

            if ($willDeactivate) {
                $institution->locations()->update([
                    'is_active' => false
                ]);
            }

            return $institution;
        });
    }

    /**
     * RF: exclui a instituição apenas quando o histórico vinculado está regularizado.
     * Uso: remoção administrativa segura do cadastro institucional.
     */
    public function delete(Institution $institution): void
    {
        DB::transaction(function () use ($institution) {

            $hasActiveBarrier = $institution
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
                throw new BusinessRuleException("Não é possível excluir esta instituição pois ela possui barreiras ativas.");
            }

            $institution->locations()->delete();

            $institution->delete();
        });
    }
}
