<?php

namespace App\Models\SpecializedEducationalSupport;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

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

    public function scopeStudent(Builder $query, $studentId): Builder
    {
        if (!is_null($studentId) && $studentId !== '') {
            $query->whereHas('students', fn($q) =>
                $q->where('students.id', $studentId)
            );
        }

        return $query;
    }

    public function scopeProfessional(Builder $query, $professionalId): Builder
    {
        if (!is_null($professionalId) && $professionalId !== '') {
            $query->where('professional_id', $professionalId);
        }

        return $query;
    }

    public function scopeType(Builder $query, $type): Builder
    {
        if (!is_null($type) && $type !== '') {
            $query->where('type', $type);
        }

        return $query;
    }

    public function scopeStatus(Builder $query, $status): Builder
    {
        if (!is_null($status) && $status !== '') {
            $query->where('status', $status);
        }

        return $query;
    }
}
