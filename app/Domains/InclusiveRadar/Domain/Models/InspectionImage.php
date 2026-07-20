<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Domain\Models;

use Database\Factories\Domains\InclusiveRadar\InspectionImageFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[UseFactory(InspectionImageFactory::class)]
final class InspectionImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'inspection_id',
        'path',
        'original_name',
        'mime_type',
        'size',
    ];

    protected $casts = [
        'size' => 'integer',
    ];

    public function inspection(): BelongsTo
    {
        return $this->belongsTo(Inspection::class, 'inspection_id');
    }
}
