<?php

declare(strict_types=1);

namespace App\Domains\Auth\Domain\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Domains\Auth\Domain\DTOs\Users\ResetUserPasswordDTO;
use App\Domains\Auth\Infrastructure\Notifications\ResetPasswordNotification;
use App\Domains\Backup\Domain\Models\Backup;
use App\Models\SpecializedEducationalSupport\Professional;
use App\Models\SpecializedEducationalSupport\Teacher;
use Database\Factories\Domains\Auth\UserFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[UseFactory(UserFactory::class)]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
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

    public function professional(): BelongsTo
    {
        return $this->belongsTo(Professional::class);
    }

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

    public function resetPassword(ResetUserPasswordDTO $data): void
    {
        $this->forceFill([
            'password' => $data->password,
            'remember_token' => $data->rememberToken,
        ]);
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function getPhotoUrlAttribute()
    {
        $photoUrl = $this->professional?->person?->photo_url
                ?? $this->teacher?->person?->photo_url;

        return $photoUrl ?? asset('images/default-user.jpg');
    }

    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }

    public function canAccessSystem(): bool
    {
        return $this->isAdmin()
            || $this->professional_id !== null
            || $this->teacher_id !== null;
    }

    public function teacher(): BelongsTo
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
            'is_admin' => 'boolean',
            'password' => 'hashed',
        ];
    }
}
