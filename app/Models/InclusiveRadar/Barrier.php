<?php

namespace App\Models\InclusiveRadar;

use App\Models\SpecializedEducationalSupport\Deficiency;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Barrier extends Model
{
    protected $fillable = [
        'name',
        'description',
        'user_id',
        'institution_id',
        'is_anonymous',
        'reporter_role',
        'barrier_category_id',
        'priority',
        'location_id',
        'latitude',
        'longitude',
        'location_specific_details',
        'barrier_status_id',
        'identified_at',
        'resolved_at',
        'is_active',
    ];

    protected $casts = [
        'identified_at' => 'date',
        'resolved_at' => 'date',
        'is_active' => 'boolean',
        'is_anonymous' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function getDisplayNameAttribute(): string
    {
        if ($this->is_anonymous) {
            return 'Contribuidor Anônimo';
        }

        return $this->user ? $this->user->name : 'Visitante';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(BarrierCategory::class, 'barrier_category_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(BarrierStatus::class, 'barrier_status_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function deficiencies(): BelongsToMany
    {
        return $this->belongsToMany(Deficiency::class, 'barrier_deficiency');
    }

    public function images(): HasMany
    {
        return $this->hasMany(BarrierImage::class);
    }
}
