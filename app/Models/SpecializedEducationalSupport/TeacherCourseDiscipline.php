<?php

namespace App\Models\SpecializedEducationalSupport;

use Illuminate\Database\Eloquent\Model;

class TeacherCourseDiscipline extends Model
{
    protected $fillable = [
        'teacher_id',
        'course_id',
        'discipline_id',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function discipline()
    {
        return $this->belongsTo(Discipline::class);
    }
}