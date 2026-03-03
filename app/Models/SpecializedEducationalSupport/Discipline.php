<?php

namespace App\Models\SpecializedEducationalSupport;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discipline extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];


    public function courses()
    {
        return $this->belongsToMany(Course::class,'course_disciplines')->withTimestamps();
    }

    // Scopes para Filtros
    public function scopeName($query, ?string $term)
    {
        if (!$term) return $query;
        return $query->where('name', 'like', "{$term}%");
    }

    public function scopeActive($query, $isActive)
    {
        if ($isActive === null || $isActive === '') return $query;
        return $query->where('is_active', (bool) $isActive);
    }

    public function scopeByCourse($query, ?int $courseId)
    {
        if (!$courseId) return $query;
        return $query->whereHas('courses', function ($q) use ($courseId) {
            $q->where('courses.id', $courseId);
        });
    }

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class); 
    }
}
