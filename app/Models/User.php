<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Backup\Backup;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\SpecializedEducationalSupport\Professional;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'professional_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function professional()
    {
        return $this->belongsTo(Professional::class);
    }
    // Atalho para pegar o nome da Person vinculada
    public function getNameAttribute()
    {
        return $this->professional?->person?->name ?? $this->attributes['name'];
    }

    public function backups(): HasMany
    {
        return $this->hasMany(Backup::class);
    }

    // Atalho para pegar a foto da Person vinculada
    public function getPhotoUrlAttribute()
    {
        return $this->professional?->person?->photo_url ?? asset('images/default-user.png');
    }

    public function hasPermission(string $permissionSlug): bool
    {
        // Se for admin total, libera tudo
        if ($this->is_admin) return true;

        return $this->professional
            ?->position
            ?->permissions
            ->contains('slug', $permissionSlug) ?? false;
    }

    public function isAdmin(): bool
    {
        return $this->is_admin;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
