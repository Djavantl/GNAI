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
        'cancellation_reason',
    ];

    protected $casts = [
        'session_date' => 'date'
    ];

   public function students()
    {
        return $this->belongsToMany(
            Student::class, 
            'attendance_session_student',
            'attendance_session_id',      
            'student_id'                  
        );
    }
    
    public function professional()
    {
        return $this->belongsTo(Professional::class);
    }

    public function sessionRecord()
    {
        return $this->hasOne(SessionRecord::class, 'attendance_session_id');
    }

    public function scopeOfStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeOfProfessional($query, $professionalId)
    {
        return $query->where('professional_id', $professionalId);
    }

    
}
