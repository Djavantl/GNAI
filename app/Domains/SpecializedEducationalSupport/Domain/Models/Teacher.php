<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Domain\Models;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\SpecializedEducationalSupport\Domain\DTOs\Teachers\CreateTeacherDTO;
use App\Domains\SpecializedEducationalSupport\Domain\DTOs\Teachers\UpdateTeacherDTO;
use App\Models\SpecializedEducationalSupport\Course;
use App\Models\SpecializedEducationalSupport\Discipline;
use App\Models\SpecializedEducationalSupport\PeiDiscipline;
use App\Models\SpecializedEducationalSupport\TeacherCourseDiscipline;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Teacher extends Model
{
    protected $fillable = [
        'person_id',
        'registration',
    ];

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
