<?php

namespace App\Models\SpecializedEducationalSupport;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseDiscipline extends Model
{
    use HasFactory;

    protected $table = 'course_disciplines';

    protected $fillable = [
        'course_id',
        'discipline_id',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function discipline()
    {
        return $this->belongsTo(Discipline::class);
    }
}
