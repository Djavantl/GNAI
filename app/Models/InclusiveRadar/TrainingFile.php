<?php

namespace App\Models\InclusiveRadar;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class TrainingFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'training_id',
        'path',
        'original_name',
        'mime_type',
        'size',
    ];

    protected static function booted()
    {
        static::deleted(function ($file) {
            if ($file->path && Storage::disk('public')->exists($file->path)) {
                Storage::disk('public')->delete($file->path);
            }
        });
    }

    public function training(): BelongsTo
    {
        return $this->belongsTo(Training::class);
    }
}
