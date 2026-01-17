<?php

namespace App\Models\InclusiveRadar;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ResourceStatus extends Model
{
    use HasFactory;

    protected $table = 'resource_statuses';

    protected $fillable = [
        'code',
        'name',
        'description',
        'blocks_loan',
        'blocks_access',
        'for_assistive_technology',
        'for_educational_material',
        'is_active',
    ];

    protected $casts = [
        'blocks_loan' => 'boolean',
        'blocks_access' => 'boolean',
        'for_assistive_technology' => 'boolean',
        'for_educational_material' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function assistiveTechnologies(): HasMany
    {
        return $this->hasMany(
            AssistiveTechnology::class,
            'status_id'
        );
    }

    public function accessibleEducationalMaterials(): HasMany
    {
        return $this->hasMany(
            AccessibleEducationalMaterial::class,
            'status_id'
        );
    }

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


    public function blocksLoan(): bool
    {
        return $this->blocks_loan;
    }

    public function blocksAccess(): bool
    {
        return $this->blocks_access;
    }
}
