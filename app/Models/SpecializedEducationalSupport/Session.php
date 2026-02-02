<?php

namespace App\Models\SpecializedEducationalSupport;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Session extends Model
{
    use SoftDeletes;
    protected $table = 'attendance_sessions';
    
    protected $fillable = [
        'student_id',
        'professional_id',
        'session_date',
        'start_time',
        'end_time',
        'type',
        'location',
        'session_objective',
        'status',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function professional()
    {
        return $this->belongsTo(Professional::class);
    }

    public function sessionRecord()
    {
        return $this->hasOne(SessionRecord::class, 'attendance_sessions_id');
    }
}
