<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccessibleEducationalMaterialImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'accessible_educational_material_id',
        'path',
        'original_name',
        'mime_type',
        'size',
    ];

    public function accessibleEducationalMaterial(): BelongsTo
    {
        return $this->belongsTo(AccessibleEducationalMaterial::class, 'accessible_educational_material_id');
    }

}
