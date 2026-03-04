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
                    /* Armazenamos fotos em subpastas por ID da vistoria para facilitar
                       a limpeza em massa do diretório durante uma eventual exclusão. */
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

    public function createInspectionForModel(Model $model, array $data): ?Inspection
    {
        $isUpdate = $model->wasRecentlyCreated === false;
        $description = $data['description'] ?? $data['inspection_description'] ?? null;
        $type = $data['type'] ?? $data['inspection_type'] ?? null;

        /* Evitamos a criação de vistorias "vazias" (sem alteração de estado, fotos ou notas)
           para manter o histórico de auditoria relevante e economizar espaço em banco. */
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

    public function delete(Inspection $inspection): void
    {
        DB::transaction(function () use ($inspection) {
            $images = $inspection->images;

            if ($images->isNotEmpty()) {
                $paths = $images->pluck('path')->toArray();

                Storage::disk('public')->delete($paths);

                $directory = "inspections/{$inspection->id}";

                /* Além de apagar os arquivos, removemos o diretório específico para evitar
                   que o storage fique poluído com pastas vazias ao longo do tempo. */
                if (Storage::disk('public')->exists($directory)) {
                    Storage::disk('public')->deleteDirectory($directory);
                }

                $inspection->images()->delete();
            }

            $inspection->delete();
        });
    }
}
