<?php

namespace App\Services\InclusiveRadar;

use App\Enums\InclusiveRadar\BarrierStatus;
use App\Enums\InclusiveRadar\InspectionType;
use App\Models\InclusiveRadar\Inspection;
use App\Enums\InclusiveRadar\ConservationState;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class InspectionService
{
    /*
    |--------------------------------------------------------------------------
    | FLUXO DE CRIAÇÃO E PERSISTÊNCIA
    |--------------------------------------------------------------------------
    */

    /**
     * REGISTRO BASE DE VISTORIA (INSPECTION)
     * * IMPORTÂNCIA: Centraliza a criação física do registro de inspeção no banco.
     * * FLUXO: Grava os dados da vistoria e gerencia o upload e vinculação de
     * evidências fotográficas no storage público.
     */
    public function createForModel(Model $model, array $data): Inspection
    {
        return DB::transaction(function () use ($model, $data) {
            $inspection = $model->inspections()->create([
                'state'           => $data['state'] ?? ConservationState::NOT_APPLICABLE->value,
                'status'          => $data['status'] ?? BarrierStatus::IDENTIFIED->value,
                'inspection_date' => $data['inspection_date'],
                'description'     => $data['description'] ?? null,
                'type'            => $data['type'],
                'user_id'         => Auth::id(),
            ]);

            if (!empty($data['images'])) {
                foreach ($data['images'] as $image) {

                    $path = $image->store("inspections/{$inspection->id}", 'public');

                    $inspection->images()->create([
                        'path'          => $path,
                        'original_name' => $image->getClientOriginalName(),
                        'mime_type'     => $image->getMimeType(),
                        'size'          => $image->getSize(),
                    ]);
                }
            }

            return $inspection;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | LÓGICA DE NEGÓCIO E AUTOMATIZAÇÃO
    |--------------------------------------------------------------------------
    */

    /**
     * ORQUESTRADOR DINÂMICO DE VISTORIAS
     * * Utilizado por: TA, MPA e MaintenanceService.
     * * IMPORTÂNCIA: Decide se uma vistoria deve ser gerada automaticamente.
     * * REGRA: Se o estado de conservação mudou, se há fotos ou descrição nova,
     * ele gera o registro (Inicial, Periódico ou de Manutenção). Caso contrário,
     * ignora a criação para evitar duplicidade de dados irrelevantes.
     */
    public function createInspectionForModel(Model $model, array $data): ?Inspection
    {
        $isUpdate = $model->wasRecentlyCreated === false;
        $description = $data['description'] ?? $data['inspection_description'] ?? null;
        $type = $data['type'] ?? $data['inspection_type'] ?? null;

        if ($isUpdate
            && !$model->wasChanged('conservation_state')
            && empty($description)
            && empty($data['images'])
        ) {
            return null;
        }

        return $this->createForModel(
            $model,
            [
                'state'           => $data['state'] ?? $model->conservation_state,
                'inspection_date' => $data['inspection_date'] ?? now(),
                'type'            => $type ?? ($isUpdate ? InspectionType::PERIODIC->value : InspectionType::INITIAL->value),
                'description'     => $description ?? ($isUpdate ? 'Atualização de estado via edição de material.' : 'Vistoria inicial de entrada.'),
                'images'          => $data['images'] ?? []
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | MANUTENÇÃO DE DADOS
    |--------------------------------------------------------------------------
    */

    /**
     * EXCLUSÃO DE VISTORIA
     * * IMPORTÂNCIA: Garante a limpeza completa dos dados.
     * * LÓGICA: Antes de apagar o registro no banco, remove fisicamente todos
     * os arquivos de imagem associados do disco para evitar acúmulo de arquivos órfãos.
     */
    public function delete(Inspection $inspection): void
    {
        DB::transaction(function () use ($inspection) {
            $images = $inspection->images;

            if ($images->isNotEmpty()) {

                $paths = $images->pluck('path')->toArray();

                Storage::disk('public')->delete($paths);

                $directory = "inspections/{$inspection->id}";

                if (Storage::disk('public')->exists($directory)) {
                    Storage::disk('public')->deleteDirectory($directory);
                }

                $inspection->images()->delete();
            }

            $inspection->delete();
        });
    }
}
