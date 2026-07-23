<?php

namespace App\Models\SpecializedEducationalSupport;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\Reportable;
use App\Models\User;
use App\Enums\SpecializedEducationalSupport\AttendanceType;

class Session extends Model
{
    use SoftDeletes;
    
    protected $table = 'attendance_sessions';
    
    protected $fillable = [
        'student_id',
        'professional_id',
        'creator_id',
        'session_date',
        'start_time',
        'end_time',
        'type',
        'attendance_type',
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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function sessionRecord()
    {
        return $this->hasOne(SessionRecord::class, 'attendance_session_id');
    }

    public function pedagogicalRecord()
    {
        return $this->hasOne(PedagogicalRecord::class, 'attendance_session_id');
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

    public function typeLabel(): string
    {
        return match(strtolower($this->type ?? '')) {
            'individual' => 'Individual',
            'group'      => 'Grupo',
            default      => ucfirst($this->type ?? ''),
        };
    }

    public function attendanceTypeLabel(): string
    {
        return AttendanceType::tryFrom($this->attendance_type ?? AttendanceType::AEE->value)?->label()
            ?? AttendanceType::AEE->label();
    }

    public function isAeeAttendance(): bool
    {
        return ($this->attendance_type ?? AttendanceType::AEE->value) === AttendanceType::AEE->value;
    }

    public function isPedagogicalAttendance(): bool
    {
        return ($this->attendance_type ?? AttendanceType::AEE->value) === AttendanceType::PEDAGOGICAL->value;
    }

    public function statusLabel(): string
    {
        return match(strtolower($this->status ?? '')) {
            'agendada', 'agendado', 'scheduled', 'pending'    => 'Agendada',
            'realizada', 'realizado', 'completed', 'confirmed' => 'Realizada',
            'cancelada', 'cancelado', 'cancelled', 'canceled'  => 'Cancelada',
            default => $this->status ?? '',
        };
    }

    public static function typeOptions(): array
    {
        return [
            'individual' => 'Individual',
            'group'      => 'Grupo',
        ];
    }

    public static function attendanceTypeOptions(): array
    {
        return AttendanceType::options();
    }

    public static function statusOptions(): array
    {
        return [
            'Agendada'  => 'Agendada',
            'Realizada' => 'Realizada',
            'Cancelada' => 'Cancelada',
        ];
    }
}
