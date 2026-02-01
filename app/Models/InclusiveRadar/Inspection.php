<?php

namespace App\Models\InclusiveRadar;

use App\Enums\InclusiveRadar\BarrierStatus;
use App\Enums\InclusiveRadar\ConservationState;
use App\Enums\InclusiveRadar\InspectionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Inspection extends Model
{
    use HasFactory;

    protected $fillable = [
        'inspectable_id',
        'inspectable_type',
        'state',
        'status',
        'inspection_date',
        'description',
        'type',
        'user_id'
    ];

    protected $casts = [
        'inspection_date' => 'date',
        'state' => ConservationState::class,
        'status' => BarrierStatus::class,
        'type' => InspectionType::class,
    ];

    public function inspectable(): MorphTo
    {
        return $this->morphTo();
    }

    public function images(): HasMany
    {
        return $this->hasMany(InspectionImage::class, 'inspection_id');
    }
}
