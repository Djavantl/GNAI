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

    // No seu Model TypeAttribute

    /**
     * Filtra pelo Rótulo (Label) do atributo
     */
    public function scopeFilterLabel($query, ?string $label)
    {
        return $label
            ? $query->where('label', 'like', "%{$label}%")
            : $query;
    }

    /**
     * Filtra se o campo é obrigatório ou não
     */
    public function scopeFilterRequired($query, $isRequired)
    {
        if (!is_null($isRequired) && $isRequired !== '') {
            $query->where('is_required', $isRequired == '1');
        }
        return $query;
    }

    /**
     * Filtra se o atributo está ativo ou inativo
     */
    public function scopeFilterActive($query, $isActive)
    {
        if (!is_null($isActive) && $isActive !== '') {
            $query->where('is_active', $isActive == '1');
        }
        return $query;
    }
}
