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
    private const DISK = 'public';

    public function store(AccessibleEducationalMaterial $material, UploadedFile $file): AccessibleEducationalMaterialImage
    {
        return DB::transaction(function () use ($material, $file) {
            $directory = "accessible-educational-materials/{$material->id}";
            $filename  = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path      = $file->storeAs($directory, $filename, self::DISK);

            return AccessibleEducationalMaterialImage::create([
                'accessible_educational_material_id' => $material->id,
                'path'          => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type'     => $file->getMimeType(),
                'size'          => $file->getSize(),
            ]);
        });
    }

    public function delete(AccessibleEducationalMaterialImage $image): AccessibleEducationalMaterial
    {
        return DB::transaction(function () use ($image) {
            $material = $image->accessibleEducationalMaterial;

            if (!$material) {
                throw new \Exception("Material educacional não encontrado para esta imagem.");
            }

            if (Storage::disk(self::DISK)->exists($image->path)) {
                Storage::disk(self::DISK)->delete($image->path);
            }

            $image->delete();
            return $material;
        });
    }
}
