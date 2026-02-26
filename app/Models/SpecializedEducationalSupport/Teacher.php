<?php

namespace App\Models\SpecializedEducationalSupport;

use Illuminate\Database\Eloquent\Model;
use App\Models\SpecializedEducationalSupport\Person;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Teacher extends Model
{
    protected $fillable = ['person_id', 'registration'];

    public function person() {
        return $this->belongsTo(Person::class);
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