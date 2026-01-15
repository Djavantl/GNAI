<?php

namespace App\Models\InclusiveRadar;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use HasFactory;

    protected $table = 'locations';

    protected $fillable = [
        'institution_id',
        'name',
        'type',
        'description',
        'latitude',
        'longitude',
        'google_place_id',

        'is_active',
    ];

    protected $casts = [
        'latitude'  => 'float',
        'longitude' => 'float',
        'is_active' => 'boolean',
    ];

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function barriers(): HasMany
    {
        return $this->hasMany(Barrier::class);
    }
}
