<?php

namespace App\Models\SpecializedEducationalSupport;

use Illuminate\Database\Eloquent\Model;
use App\Models\SpecializedEducationalSupport\Person;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\Reportable;


class Teacher extends Model
{
    use Reportable;

    protected $fillable = ['person_id', 'registration'];

    public function person() {
        return $this->belongsTo(Person::class);
    }

    public function user()
    {
        return $this->hasOne(User::class, 'teacher_id');
    }

    public function disciplines() {
        return $this->belongsToMany(Discipline::class);
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'teacher_courses')->withTimestamps();
    }

    public function scopeName($query, ?string $term)
    {
        if (!$term) return $query;

        return $query->whereHas('person', function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%");
        });
    }

    public function scopeEmail($query, ?string $term)
    {
        if (!$term) return $query;

        return $query->whereHas('person', function ($q) use ($term) {
            $q->where('email', 'like', "%{$term}%");
        });
    }

    public function scopeRegistration($query, ?string $term)
    {
        if (!$term) return $query;

        return $query->where('registration', 'like', "%{$term}%");
    }
}