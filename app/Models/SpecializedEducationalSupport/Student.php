<?php

namespace App\Models\SpecializedEducationalSupport;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\GlobalSearchable;

class Student extends Model
{
    use GlobalSearchable;

    protected $fillable = [
        'person_id',
        'registration',
        'student_code',
        'entry_date',
        'status',
        'education_level',
        'modality',
        'notes',
    ];

    protected array $searchable = [
        'registration',
        'status',
        'person.name',
        'person.email',
    ];

    protected array $searchAliases = [
        'ativo' => ['active'],
        'trancado' => ['locked'],
        'concluido' => ['completed'],
        'concluído' => ['completed'],
        'evadido' => ['dropped'],
    ];


    // Relacionamentos

    // pessoa

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    // responsaveis

    public function guardians()
    {
        return $this->hasMany(Guardian::class);
    }

    public function documents()
    {
        return $this->hasMany(StudentDocument::class);
    }

    // Contexto educacional

    public function contexts()
    {
        return $this->hasMany(StudentContext::class);
    }

    public function currentContext()
    {
        // Retorna apenas um registro onde is_current é verdadeiro
        return $this->hasOne(StudentContext::class)->where('is_current', true);
    }

   // Deficiências do aluno

    public function deficiencies()
    {
        return $this->hasMany(StudentDeficiencies::class, 'student_id');
    }

    public function peis()
    {
        return $this->hasMany(Pei::class, 'student_id');
    }

    public function studentCourses()
    {
        return $this->hasMany(StudentCourse::class);
    }

    public function sessions()
    {
        return $this->belongsToMany(
            Session::class, 
            'attendance_session_student', 
            'student_id',                 
            'attendance_session_id'       
        );
    }

    // Cursos do aluno 
    public function courses()
    {
        return $this->belongsToMany(
            Course::class,
            'student_courses'
        )
        ->withPivot(['academic_year', 'is_current', 'status'])
        ->withTimestamps();
    }

    // Curso atual do aluno
    public function currentCourse()
    {
        return $this->belongsToMany(
            Course::class,
            'student_courses'
        )
        ->wherePivot('is_current', true)
        ->withPivot(['academic_year', 'status'])
        ->withTimestamps();
    }

    // Helpers

    public static function statusOptions(): array
    {
        return [
            'active'    => 'Ativo',
            'locked'    => 'Trancado',
            'completed' => 'Concluído',
            'dropped'   => 'Evadido',
        ];
    }
}
