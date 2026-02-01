<?php

namespace App\Models\InclusiveRadar;

use App\Models\SpecializedEducationalSupport\Deficiency;
use App\Enums\InclusiveRadar\ConservationState;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssistiveTechnology extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'assistive_technologies';

    protected $fillable = [
        'name',
        'description',
        'type_id',
        'asset_code',
        'quantity',
        'quantity_available',
        'requires_training',
        'conservation_state',
        'notes',
        'status_id',
        'is_active',
    ];

    protected $casts = [
        'requires_training' => 'boolean',
        'is_active' => 'boolean',
        'conservation_state' => ConservationState::class,
    ];

    public function type(): BelongsTo
    {
        return $this->belongsTo(ResourceType::class, 'type_id');
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

    public function attributeValues(): MorphMany
    {
        return $this->morphMany(ResourceAttributeValue::class, 'resource');
    }

    public function inspections(): MorphMany
    {
        return $this->morphMany(Inspection::class, 'inspectable')
            ->with('images')
            ->orderByDesc('inspection_date')
            ->orderByDesc('created_at');
    }

    public function allImages(): HasManyThrough
    {
        return $this->hasManyThrough(
            InspectionImage::class,
            Inspection::class,
            'inspectable_id',
            'inspection_id',
            'id',
            'id'
        )->where('inspectable_type', static::class);
    }

    public function loans(): MorphMany
    {
        return $this->morphMany(Loan::class, 'loanable');
    }
}
