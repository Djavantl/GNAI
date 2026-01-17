<?php

namespace App\Services\InclusiveRadar;

use App\Models\InclusiveRadar\AssistiveTechnology;
use App\Models\InclusiveRadar\AssistiveTechnologyImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AssistiveTechnologyImageService
{
    private const DISK = 'public';

    public function store(AssistiveTechnology $assistiveTechnology, UploadedFile $file): AssistiveTechnologyImage
    {
        return DB::transaction(function () use ($assistiveTechnology, $file) {
            $directory = "assistive-technologies/{$assistiveTechnology->id}";
            $filename  = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path      = $file->storeAs($directory, $filename, self::DISK);

            return AssistiveTechnologyImage::create([
                'assistive_technology_id' => $assistiveTechnology->id,
                'path'                     => $path,
                'original_name'            => $file->getClientOriginalName(),
                'mime_type'                => $file->getMimeType(),
                'size'                     => $file->getSize(),
            ]);
        });
    }

    public function delete(AssistiveTechnologyImage $image): AssistiveTechnology
    {
        return DB::transaction(function () use ($image) {
            $assistiveTechnology = $image->assistiveTechnology;
            if (!$assistiveTechnology) {
                throw new \Exception("Não foi possível localizar a tecnologia vinculada a esta imagem.");
            }
            if (Storage::disk(self::DISK)->exists($image->path)) {
                Storage::disk(self::DISK)->delete($image->path);
            }
            $image->delete();
            return $assistiveTechnology;
        });
    }
}
