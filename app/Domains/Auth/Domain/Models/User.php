<?php

declare(strict_types=1);

namespace App\Domains\Auth\Domain\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Domains\Audit\Domain\Contracts\Auditable;
use App\Domains\Audit\Domain\ValueObjects\AuditedField;
use App\Domains\Audit\Infrastructure\Eloquent\AuditsChanges;
use App\Domains\Auth\Domain\DTOs\Users\ResetUserPasswordDTO;
use App\Domains\Auth\Infrastructure\Notifications\ResetPasswordNotification;
use App\Domains\Backup\Domain\Models\Backup;
use App\Domains\Inclusion\Domain\Models\Loan;
use App\Domains\Inclusion\Domain\Models\Professional;
use App\Domains\Inclusion\Domain\Models\Teacher;
use App\Domains\Inclusion\Domain\Models\Waitlist;
use Database\Factories\Domains\Auth\UserFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[UseFactory(UserFactory::class)]
class User extends Authenticatable implements Auditable
{
    use AuditsChanges;

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

    public static function auditType(): string
    {
        return 'user';
    }

    public static function auditLabel(): string
    {
        return 'Usuário';
    }

    public static function auditedFields(): array
    {
        return [
            'name' => AuditedField::text('Nome'),
            'email' => AuditedField::text('E-mail'),
            'photo' => AuditedField::file('Foto'),
            'password' => AuditedField::secret('Senha'),
            'email_verified_at' => AuditedField::dateTime('E-mail verificado em'),
            'is_admin' => AuditedField::boolean('Administrador'),
            'is_super_admin' => AuditedField::boolean('Administrador superior'),
            'role' => AuditedField::enum('Perfil', valueLabels: [
                'admin' => 'Administrador',
                'professional' => 'Profissional',
                'teacher' => 'Professor',
            ]),
            'professional_id' => AuditedField::reference('Profissional vinculado', Professional::class, 'person.name'),
            'teacher_id' => AuditedField::reference('Professor vinculado', Teacher::class, 'person.name'),
        ];
    }

    /** @return list<string> */
    public static function unauditedFields(): array
    {
        return ['remember_token'];
    }

    public function auditDescription(): string
    {
        return "o usuário {$this->name}";
    }

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

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class, 'user_id');
    }

    public function waitlists(): HasMany
    {
        return $this->hasMany(Waitlist::class, 'user_id');
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
        if ($this->hasPersonProfile()) {
            $photoUrl = $this->professional?->person?->photo_url
                    ?? $this->teacher?->person?->photo_url;

            return $photoUrl ?? asset('images/default-user.jpg');
        }

        return filled($this->photo)
            ? asset('storage/'.$this->photo)
            : asset('images/default-user.jpg');
    }

    /**
     * Profissionais e professores mantêm seus dados no cadastro de pessoa; as
     * demais contas (como o administrador superior) têm apenas o perfil de conta.
     */
    public function hasPersonProfile(): bool
    {
        return $this->professional_id !== null || $this->teacher_id !== null;
    }

    /**
     * Administrador interno da unidade ou administrador superior do Gnai.
     */
    public function isAdmin(): bool
    {
        return (bool) $this->is_admin || $this->isSuperAdmin();
    }

    /**
     * Conta de controle do Gnai, criada e mantida pelo terminal. Não é
     * atribuível por formulários (fora de $fillable).
     */
    public function isSuperAdmin(): bool
    {
        return (bool) $this->is_super_admin;
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
            'is_super_admin' => 'boolean',
            'password' => 'hashed',
        ];
    }
}
