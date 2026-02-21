<?php

namespace App\Models\InclusiveRadar;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BarrierCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'barrier_categories';

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function barriers(): HasMany
    {
        return $this->hasMany(Barrier::class);
    }

    public function scopeFilterName($query, ?string $name)
    {
        return $name ? $query->where('name', 'like', "%{$name}%") : $query;
    }

    public function scopeFilterActive($query, $isActive)
    {
        if (!is_null($isActive) && $isActive !== '') {
            $query->where('is_active', $isActive == '1');
        }
        return $query;
    }
}
