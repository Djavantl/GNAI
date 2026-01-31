<?php

namespace App\Models\InclusiveRadar;

use App\Models\SpecializedEducationalSupport\Deficiency;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccessibleEducationalMaterial extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'accessible_educational_materials';

    protected $fillable = [
        'title',
        'type_id',
        'asset_code',
        'quantity',
        'quantity_available',
        'requires_training',
        'notes',
        'status_id',
        'is_active',
    ];

    protected $casts = [
        'requires_training' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function loans(): MorphMany
    {
        return $this->morphMany(Loan::class, 'loanable');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(ResourceType::class, 'type_id');
    }

    public function resourceStatus(): BelongsTo
    {
        return $this->belongsTo(ResourceStatus::class, 'status_id');
    }

    public function attributeValues(): HasMany
    {
        return $this->hasMany(ResourceAttributeValue::class, 'resource_id')
            ->where('resource_type', 'accessible_educational_material');
    }

    public function deficiencies(): BelongsToMany
    {
        return $this->belongsToMany(
            Deficiency::class,
            'accessible_educational_material_deficiency',
            'accessible_educational_material_id',
            'deficiency_id'
        );
    }

    public function accessibilityFeatures(): BelongsToMany
    {
        return $this->belongsToMany(
            AccessibilityFeature::class,
            'accessible_educational_material_accessibility',
            'accessible_educational_material_id',
            'accessibility_feature_id'
        );
    }

    public function images(): HasMany
    {
        return $this->hasMany(AccessibleEducationalMaterialImage::class, 'accessible_educational_material_id');
    }
}
