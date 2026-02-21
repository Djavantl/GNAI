<?php

namespace App\Models\InclusiveRadar;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    use HasFactory, SoftDeletes;

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

    /**
     * Filtra pelo nome do ponto de referência
     */
    public function scopeFilterName(Builder $query, ?string $name): Builder
    {
        return $name ? $query->where('name', 'like', "%{$name}%") : $query;
    }

    /**
     * Filtra pelo nome da instituição através do relacionamento
     */
    public function scopeFilterInstitution(Builder $query, ?string $institutionName): Builder
    {
        if ($institutionName) {
            return $query->whereHas('institution', function ($q) use ($institutionName) {
                $q->where('name', 'like', "%{$institutionName}%");
            });
        }
        return $query;
    }

    /**
     * Filtra pelo status ativo/inativo
     */
    public function scopeFilterActive(Builder $query, $isActive): Builder
    {
        if (!is_null($isActive) && $isActive !== '') {
            $query->where('is_active', $isActive == '1');
        }
        return $query;
    }
}
