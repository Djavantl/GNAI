<?php

namespace App\Models\InclusiveRadar;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BarrierImage extends Model
{
    use HasFactory;

    protected $table = 'barrier_images';

    protected $fillable = [
        'barrier_id',
        'path',
        'original_name',
        'mime_type',
        'size',
        'is_before',
    ];

    protected $casts = [
        'is_before' => 'boolean',
        'size' => 'integer',
    ];

    public function barrier(): BelongsTo
    {
        return $this->belongsTo(Barrier::class);
    }
}
