<?php

namespace App\Models\SpecializedEducationalSupport;

use Illuminate\Database\Eloquent\Model;

class Pei extends Model
{
    protected $fillable = [
        'student_id', 
        'professional_id', 
        'student_context_id', 
        'semester_id', 
        'is_finished',
    ];

    public function adaptations()
    {
        return $this->hasMany(PeiAdaptation::class);
    }

    public function studentContext()
    {
        return $this->belongsTo(StudentContext::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
