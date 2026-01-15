<?php

namespace App\Services\InclusiveRadar;

use App\Models\InclusiveRadar\Barrier;
use App\Models\InclusiveRadar\BarrierImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BarrierImageService
{

    public function store(Barrier $barrier, UploadedFile $file): BarrierImage
    {
        return DB::transaction(function () use ($barrier, $file) {
            $directory = "barriers/{$barrier->id}";
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs($directory, $filename, 'public');

            return BarrierImage::create([
                'barrier_id'    => $barrier->id,
                'path'          => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type'     => $file->getMimeType(),
                'size'          => $file->getSize(),
                'is_before'     => true,
            ]);
        });
    }

    public function delete(BarrierImage $image): void
    {
        DB::transaction(function () use ($image) {
            Storage::disk('public')->delete($image->path);
            $image->delete();
        });
    }
}
