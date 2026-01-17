<?php

namespace App\Models\InclusiveRadar;

use App\Models\SpecializedEducationalSupport\Deficiency;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class AssistiveTechnology extends Model
{
    use HasFactory;

    protected $table = 'assistive_technologies';

    protected $fillable = [
        'name',
        'description',
        'type_id',
        'asset_code',
        'conservation_state',
        'requires_training',
        'notes',
        'status_id',
        'is_active',
    ];

    protected $casts = [
        'requires_training' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function type(): BelongsTo
    {
        return $this->belongsTo(ResourceType::class, 'type_id');
    }

    public function attributeValues(): HasMany
    {
        return $this->hasMany(ResourceAttributeValue::class, 'resource_id')
            ->where('resource_type', 'assistive_technology');
    }

    public function resourceStatus(): BelongsTo
    {
        return $this->belongsTo(ResourceStatus::class, 'status_id');
    }

    public function deficiencies(): BelongsToMany
    {
        return $this->belongsToMany(
            Deficiency::class,
            'assistive_technology_deficiency',
            'assistive_technology_id',
            'deficiency_id'
        );
    }

    public function images(): HasMany
    {
        return $this->hasMany(AssistiveTechnologyImage::class, 'assistive_technology_id');
    }
}
