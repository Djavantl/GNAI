<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AccessibleEducationalMaterialStatus extends Model
{
    use HasFactory;

    protected $table = 'accessible_educational_material_statuses';

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function accessibleEducationalMaterials(): HasMany
    {
        return $this->hasMany(
            AccessibleEducationalMaterial::class,
            'accessible_educational_material_status_id'
        );
    }
}
