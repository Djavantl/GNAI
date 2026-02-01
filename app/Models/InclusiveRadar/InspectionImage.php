<?php

namespace App\Models\InclusiveRadar;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class InspectionImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'inspection_id',
        'path',
        'original_name',
        'mime_type',
        'size'
    ];

    protected static function booted()
    {
        static::deleted(function ($image) {
            if ($image->path && Storage::disk('public')->exists($image->path)) {
                Storage::disk('public')->delete($image->path);
            }
        });
    }

    public function inspection(): BelongsTo
    {
        return $this->belongsTo(Inspection::class, 'inspection_id');
    }
}
