<?php

namespace App\Services\InclusiveRadar;

use App\Exceptions\InclusiveRadar\CannotDeleteLinkedBarrierException;
use App\Models\InclusiveRadar\Institution;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InstitutionService
{
    public function store(array $data): ?Institution
    {
        /* O sistema é projetado para gerenciar uma única instituição por instância.
           Se já existir um registro, bloqueamos a criação de duplicatas. */
        if (Institution::exists()) {
            return null;
        }

        return DB::transaction(fn () => Institution::create($data));
    }

    public function update(Institution $institution, array $data): Institution
    {
        return DB::transaction(function () use ($institution, $data) {

            $wasActive = $institution->is_active;
            $willDeactivate = $wasActive && isset($data['is_active']) && !$data['is_active'];

            if ($willDeactivate) {
                /* Impedimos a desativação se houver pendências em aberto, garantindo que
                   a instituição mantenha responsabilidade sobre barreiras não resolvidas. */
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
                /* Ao desativar a instituição, aplicamos o efeito cascata nos locais
                   para manter a consistência da disponibilidade no radar. */
                $institution->locations()->update([
                    'is_active' => false
                ]);
            }

            return $institution;
        });
    }

    public function delete(Institution $institution): void
    {
        DB::transaction(function () use ($institution) {

            $hasActiveBarrier = $institution
                ->barriers()
                ->get()
                ->contains(function ($barrier) {
                    $status = $barrier->latestStatus();

                    /* Se não houver status ou se o status atual for impeditivo (ex: em análise),
                       a exclusão da instituição é abortada para evitar perda de rastro. */
                    if (!$status) {
                        return true;
                    }

                    return ! $status->allowsDeletion();
                });

            if ($hasActiveBarrier) {
                throw new CannotDeleteLinkedBarrierException();
            }

            // Remove todos os pontos de referencias desta instituicao
            $institution->locations()->delete();

            $institution->delete();
        });
    }
}
