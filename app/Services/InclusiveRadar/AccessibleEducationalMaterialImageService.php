<?php

namespace App\Services\InclusiveRadar;

use App\Models\InclusiveRadar\AccessibleEducationalMaterial;
use App\Models\InclusiveRadar\AccessibleEducationalMaterialImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AccessibleEducationalMaterialImageService
{

    public function store(
        AccessibleEducationalMaterial $material,
        UploadedFile $file
    ): AccessibleEducationalMaterialImage {
        return DB::transaction(function () use ($material, $file) {
            $directory = "accessible-educational-materials/{$material->id}";
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs($directory, $filename, 'public');
            return AccessibleEducationalMaterialImage::create([
                'accessible_educational_material_id' => $material->id,
                'path'          => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type'     => $file->getMimeType(),
                'size'          => $file->getSize(),
            ]);
        });
    }

    public function delete(AccessibleEducationalMaterialImage $image): void
    {
        DB::transaction(function () use ($image) {
            if (Storage::disk('public')->exists($image->path)) {
                Storage::disk('public')->delete($image->path);
            }

            $image->delete();
        });
    }
}
