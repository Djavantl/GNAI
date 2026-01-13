<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssistiveTechnology extends Model
{
    use HasFactory;

    protected $table = 'assistive_technologies';

    protected $fillable = [
        'name',
        'description',
        'type',
        'quantity',
        'asset_code',
        'conservation_state',
        'requires_training',
        'notes',
        'assistive_technology_status_id',
        'is_active',
    ];

    protected $casts = [
        'requires_training' => 'boolean',
        'quantity' => 'integer',
        'is_active' => 'boolean',
    ];

    public function status(): BelongsTo
    {
        return $this->belongsTo(
            AssistiveTechnologyStatus::class,
            'assistive_technology_status_id'
        );
    }

    public function deficiencies(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Deficiency::class, 'assistive_technology_deficiency');
    }

    public function images(): HasMany
    {
        return $this->hasMany(AssistiveTechnologyImage::class);
    }
}
