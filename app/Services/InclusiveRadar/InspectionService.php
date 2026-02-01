<?php

namespace App\Services\InclusiveRadar;

use App\Enums\InclusiveRadar\BarrierStatus;
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
                    $path = $image->store('inspections', 'public');

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

    public function delete(Inspection $inspection): void
    {
        DB::transaction(function () use ($inspection) {
            $images = $inspection->images;

            if ($images->isNotEmpty()) {
                $paths = $images->pluck('path')->toArray();
                Storage::disk('public')->delete($paths);
                $inspection->images()->delete();
            }

            $inspection->delete();
        });
    }

}
