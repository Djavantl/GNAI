<?php

namespace App\Models\InclusiveRadar;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeAttributeAssignment extends Model
{
    use HasFactory;

    protected $table = 'type_attribute_assignments';

    protected $fillable = [
        'type_id',
        'attribute_id',
    ];

    public function type()
    {
        return $this->belongsTo(ResourceType::class, 'type_id');
    }

    public function attribute()
    {
        return $this->belongsTo(TypeAttribute::class, 'attribute_id');
    }

    public function scopeFilterName(Builder $query, ?string $name)
    {
        return $name ? $query->where('name', 'like', "%{$name}%") : $query;
    }

    public function scopeFilterDigital(Builder $query, $isDigital)
    {
        if (!is_null($isDigital) && $isDigital !== '') {
            $query->where('is_digital', $isDigital == '1');
        }
        return $query;
    }

    public function scopeFilterActive(Builder $query, $isActive)
    {
        if (!is_null($isActive) && $isActive !== '') {
            $query->where('is_active', $isActive == '1');
        }
        return $query;
    }
}
