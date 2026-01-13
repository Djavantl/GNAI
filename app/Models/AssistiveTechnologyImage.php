<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssistiveTechnologyImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'assistive_technology_id',
        'path',
        'original_name',
        'mime_type',
        'size',
    ];

    public function assistiveTechnology(): BelongsTo
    {
        return $this->belongsTo(AssistiveTechnology::class);
    }
}
