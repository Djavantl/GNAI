<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AccessibleEducationalMaterial extends Model
{
    use HasFactory;

    protected $table = 'accessible_educational_materials';

    protected $fillable = [
        'title',
        'type',
        'format',
        'language',
        'isbn',
        'publisher',
        'edition',
        'publication_date',
        'pages',
        'accessibility_features',
        'asset_code',
        'location',
        'conservation_state',
        'requires_training',
        'cost',
        'accessible_educational_material_status_id',
        'is_active',
    ];

    protected $casts = [
        'publication_date' => 'date',
        'pages' => 'integer',
        'accessibility_features' => 'array',
        'requires_training' => 'boolean',
        'is_active' => 'boolean',
        'cost' => 'decimal:2',
    ];

    public function status(): BelongsTo
    {
        return $this->belongsTo(
            AccessibleEducationalMaterialStatus::class,
            'accessible_educational_material_status_id'
        );
    }

    public function deficiencies(): BelongsToMany
    {
        return $this->belongsToMany(
            Deficiency::class,
            'accessible_educational_material_deficiency'
        );
    }

    public function accessibilities(): BelongsToMany
    {
        return $this->belongsToMany(
            AccessibilityFeature::class,
            'accessible_educational_material_accessibility'
        );
    }

    public function images()
    {
        return $this->hasMany(AccessibleEducationalMaterialImage::class);
    }
}
