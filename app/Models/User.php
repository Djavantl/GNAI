<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Backup\Backup;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\SpecializedEducationalSupport\Professional;
use App\Models\SpecializedEducationalSupport\Teacher;

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
        'teacher_id',  
        'is_admin',     
        'role',
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
        return $this->professional?->person?->name
            ?? $this->teacher?->person?->name
            ?? $this->attributes['name'];
    }

    public function backups(): HasMany
    {
        return $this->hasMany(Backup::class);
    }

    public function getPhotoUrlAttribute()
    {
        $photoUrl = $this->professional?->person?->photo_url 
                ?? $this->teacher?->person?->photo_url;

        return $photoUrl ?? asset('images/default-user.jpg');
    }

    public function hasPermission(string $permissionSlug): bool
    {

        if ($this->is_admin) return true;

        $hasProfessionalPermission = $this->professional
            ?->position
            ?->permissions
            ->contains('slug', $permissionSlug) ?? false;

        if ($hasProfessionalPermission) return true;

        if ($this->teacher_id) {
            return \DB::table('teacher_global_permissions')
                ->join('permissions', 'permissions.id', '=', 'teacher_global_permissions.permission_id')
                ->where('permissions.slug', $permissionSlug)
                ->exists();
        }

        return false;
    }

    public function isAdmin(): bool
    {
        return $this->is_admin;
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
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
