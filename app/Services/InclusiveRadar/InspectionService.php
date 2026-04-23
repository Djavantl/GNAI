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
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class InspectionService
{
    /**
     * RF: cria uma inspeção completa, incluindo armazenamento otimizado de imagens.
     * Uso: vistorias de barreiras, materiais e tecnologias assistivas.
     */
    public function createForModel(Model $model, array $data): Inspection
    {
        return DB::transaction(function () use ($model, $data) {
            $inspection = $model->inspections()->create([
                'state' => $data['state'] ?? ConservationState::NOT_APPLICABLE->value,
                'status' => $data['status'] ?? BarrierStatus::IDENTIFIED->value,
                'inspection_date' => $data['inspection_date'],
                'description' => $data['description'] ?? null,
                'type' => $data['type'],
                'user_id' => Auth::id(),
            ]);

            if (!empty($data['images'])) {
                $manager = new ImageManager(new Driver());

                foreach ($data['images'] as $image) {
                    $name = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                    $fileName = $name . '_' . uniqid() . '.webp';
                    $directory = "inspections/{$inspection->id}";
                    $path = "{$directory}/{$fileName}";

                    $optimizedImage = $manager->read($image)
                        ->scale(width: 600)
                        ->toWebp(60);

                    Storage::disk('public')->put($path, (string) $optimizedImage);

                    $inspection->images()->create([
                        'path' => $path,
                        'original_name' => $image->getClientOriginalName(),
                        'mime_type' => 'image/webp',
                        'size' => strlen((string) $optimizedImage),
                    ]);
                }
            }

            return $inspection;
        });
    }

    /**
     * RF: cria inspeção somente quando a alteração realmente produz histórico útil.
     * Uso: cadastros e edições de itens que disparam vistoria automática.
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
                'state' => $data['state'] ?? $model->conservation_state,
                'inspection_date' => $data['inspection_date'] ?? now(),
                'type' => $type ?? ($isUpdate ? InspectionType::PERIODIC->value : InspectionType::INITIAL->value),
                'description' => $description ?? ($isUpdate ? 'Atualização de estado via edição de material.' : 'Vistoria inicial de entrada.'),
                'images' => $data['images'] ?? []
            ]
        );
    }

    /**
     * RF: remove a inspeção e todos os arquivos vinculados a ela.
     * Uso: exclusão administrativa de vistorias registradas incorretamente.
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
