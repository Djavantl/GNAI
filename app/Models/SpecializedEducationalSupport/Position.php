<?php

namespace App\Models\SpecializedEducationalSupport;
use App\Models\Permission;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Reportable;
use DomainException;

class Position extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];


    protected $casts = [
        'is_active' => 'boolean',
    ];

    // protected static function booted() {
    //     static::addGlobalScope('is_active', function ($query) {
    //         $query->where('is_active', true);
    //     });
    // }

    public function professionals(): HasMany
    {
        return $this->hasMany(Professional::class);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_position');
    }

    public function scopeName($query, ?string $term)
    {
        if (!$term) return $query;

        return $query->where('name', 'like', "{$term}%");
    }

    public function scopeDescription($query, ?string $term)
    {
        if (!$term) return $query;

        return $query->where('description', 'like', "%{$term}%");
    }

    public function scopeActive($query, $isActive)
    {
        if ($isActive === null || $isActive === '') return $query;

        return $query->where('is_active', (bool) $isActive);
    }

    public function ensureCanBeDeactivated(): void
    {
        if ($this->professionals()->exists()) {
            throw new DomainException(
                "Este cargo está vinculado a um ou mais profissionais e não pode ser desativado."
            );
        }
    }

    public function ensureIsActive(): void
    {
        if (! $this->is_active) {
            throw new DomainException(
                "Este cargo está desativado e não pode ser vinculado a um profissional."
            );
        }
    }

    public function ensureCanBeDeleted(): void
    {
        if ($this->professionals()->exists()) {

            throw new DomainException(
                "Este cargo está vinculado a um ou mais profissionais e não pode ser removido."
            );

        }
    }
}
