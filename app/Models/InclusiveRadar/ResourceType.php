<?php

namespace App\Models\InclusiveRadar;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResourceType extends Model
{
    use HasFactory;

    protected $table = 'resource_types';

    protected $fillable = [
        'name',
        'for_assistive_technology',
        'for_educational_material',
        'is_digital',
        'is_active',
    ];

    protected $casts = [
        'for_assistive_technology' => 'boolean',
        'for_educational_material' => 'boolean',
        'is_digital' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForAssistiveTechnology($query)
    {
        return $query->where('for_assistive_technology', true);
    }

    public function scopeForEducationalMaterial($query)
    {
        return $query->where('for_educational_material', true);
    }

    public function scopeDigital($query)
    {
        return $query->where('is_digital', true);
    }
}
