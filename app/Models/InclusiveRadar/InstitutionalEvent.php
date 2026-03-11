<?php

namespace App\Models\InclusiveRadar;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class InstitutionalEvent extends Model
{
    use HasFactory;

    protected $table = 'institutional_events';

    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'location',
        'organizer',
        'audience',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'is_active' => 'boolean',
    ];

    /**
     * Scope para buscar por título
     */
    public function scopeSearchTitle(Builder $query, ?string $title): Builder
    {
        if ($title) {
            return $query->where('title', 'like', "%{$title}%");
        }
        return $query;
    }

    /**
     * Scope para eventos ativos
     */
    public function scopeActive(Builder $query, bool $active = true): Builder
    {
        return $query->where('is_active', $active);
    }
}
