<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AccessibilityFeature extends Model
{
    use HasFactory;

    protected $table = 'accessibility_features';

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function materials(): BelongsToMany
    {
        return $this->BelongsToMany(
            AccessibleEducationalMaterial::class,
            'accessible_educational_material_accessibility'
        );
    }
}
