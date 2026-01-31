<?php

namespace App\Models\SpecializedEducationalSupport;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentDeficiencies extends Model
{
    use HasFactory;

    protected $table = 'students_deficiencies';

    protected $fillable = [
        'student_id',
        'deficiency_id',
        'severity',
        'uses_support_resources',
        'notes',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function deficiency()
    {
        return $this->belongsTo(Deficiency::class);
    }
}
