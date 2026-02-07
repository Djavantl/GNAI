<?php

namespace App\Models\SpecializedEducationalSupport;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    public function studentCourses()
    {
        return $this->hasMany(StudentCourse::class);
    }
    public function disciplines()
    {
        return $this->belongsToMany(Discipline::class,'course_disciplines')->withTimestamps();
    }
}
