<?php

namespace App\Models\SpecializedEducationalSupport;

use Illuminate\Database\Eloquent\Model;

class Deficiency extends Model
{
    protected $fillable = [
        'name',
        'cid_code',
        'description',
        'is_active',
    ];


    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function students()
    {
        return $this->belongsToMany(Student::class, 'students_deficiencies')
            ->using(StudentDeficiencies::class)
            ->withPivot([
                'id',
                'severity',
                'uses_support_resources',
                'notes'
            ])
            ->withTimestamps();
    }

    // Scopes para Filtros
    public function scopeName($query, ?string $term)
    {
        if (!$term) return $query;
        return $query->where('name', 'like', "{$term}%");
    }

    public function scopeCid($query, ?string $term)
    {
        if (!$term) return $query;
        return $query->where('cid_code', 'like', "{$term}%");
    }

    public function scopeActive($query, $isActive)
    {
        if ($isActive === null || $isActive === '') return $query;
        return $query->where('is_active', (bool) $isActive);
    }
}
