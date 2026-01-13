<?php

namespace App\Services;

use App\Models\AssistiveTechnology;
use App\Models\AssistiveTechnologyImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AssistiveTechnologyImageService
{
    public function store(
        AssistiveTechnology $technology,
        UploadedFile $file
    ): AssistiveTechnologyImage {
        return DB::transaction(function () use ($technology, $file) {

            $directory = "assistive-technologies/{$technology->id}";

            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

            $path = $file->storeAs($directory, $filename, 'public');

            return AssistiveTechnologyImage::create([
                'assistive_technology_id' => $technology->id,
                'path'          => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type'     => $file->getMimeType(),
                'size'          => $file->getSize(),
            ]);
        });
    }

    public function delete(AssistiveTechnologyImage $image): void
    {
        DB::transaction(function () use ($image) {
            Storage::disk('public')->delete($image->path);
            $image->delete();
        });
    }
}
