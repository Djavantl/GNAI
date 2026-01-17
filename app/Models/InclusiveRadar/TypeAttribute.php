<?php

namespace App\Models\InclusiveRadar;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeAttribute extends Model
{
    use HasFactory;

    protected $table = 'type_attributes';

    protected $fillable = [
        'name',
        'label',
        'field_type',
        'is_required',
        'is_active',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function types()
    {
        return $this->belongsToMany(
            ResourceType::class,
            'type_attribute_assignments',
            'attribute_id',
            'type_id'
        )->withTimestamps();
    }
}
