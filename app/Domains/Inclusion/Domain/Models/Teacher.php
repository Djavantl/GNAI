<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Domain\Models;

use App\Domains\Audit\Domain\Contracts\Auditable;
use App\Domains\Audit\Domain\ValueObjects\AuditedField;
use App\Domains\Audit\Infrastructure\Eloquent\AuditsChanges;
use App\Domains\Auth\Domain\Models\User;
use App\Domains\Inclusion\Domain\DTOs\Teachers\CreateTeacherDTO;
use App\Domains\Inclusion\Domain\DTOs\Teachers\UpdateTeacherDTO;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Teacher extends Model implements Auditable
{
    use AuditsChanges;

    protected $fillable = [
        'person_id',
        'registration',
    ];

    public static function auditType(): string
    {
        return 'teacher';
    }

    public static function auditLabel(): string
    {
        return 'Professor';
    }

    public static function auditedFields(): array
    {
        return [
            'person_id' => AuditedField::reference('Pessoa', Person::class),
            'registration' => AuditedField::text('Matrícula'),
        ];
    }

    public function auditDescription(): string
    {
        return 'o professor '.($this->person?->name ?? "#{$this->getKey()}");
    }

    public static function register(CreateTeacherDTO $data): self
    {
        return new self([
            'person_id' => $data->personId,
            'registration' => $data->registration->value(),
        ]);
    }

    public function revise(UpdateTeacherDTO $data): void
    {
        $this->fill([
            'registration' => $data->registration->value(),
        ]);
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'teacher_id');
    }

    public function disciplines(): BelongsToMany
    {
        return $this->belongsToMany(
            Discipline::class,
            'teacher_course_disciplines',
            'teacher_id',
            'discipline_id',
        );
    }

    public function courseDisciplines(): HasMany
    {
        return $this->hasMany(TeacherCourseDiscipline::class);
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'teacher_courses')->withTimestamps();
    }

    public function peiDisciplines(): HasMany
    {
        return $this->hasMany(PeiDiscipline::class, 'teacher_id');
    }
}
