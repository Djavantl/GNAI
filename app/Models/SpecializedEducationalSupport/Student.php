<?php

namespace App\Models\SpecializedEducationalSupport;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\GlobalSearchable;
use App\Models\Traits\Auditable; 
use App\Models\AuditLog;       
use Illuminate\Database\Eloquent\Relations\MorphMany;

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

    /**
     * Relacionamento de Logs
     */
    public function logs(): MorphMany
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }

    /**
     * Nomes amigáveis para os campos na auditoria
     */
    public static function getAuditLabels(): array
    {
        return [
            'person_id'       => 'Pessoa/Usuário',
            'registration'    => 'Matrícula',
            'student_code'    => 'Código do Aluno',
            'entry_date'      => 'Data de Ingresso',
            'status'          => 'Status Acadêmico',
            'education_level' => 'Nível de Escolaridade',
            'modality'        => 'Modalidade',
            'notes'           => 'Observações',
        ];
    }

    /**
     * Formatação de valores específicos para exibição no log
     */
    public static function formatAuditValue(string $field, $value): ?string
    {
        if ($field === 'status') {
            return self::statusOptions()[$value] ?? $value;
        }

        if ($field === 'person_id') {
            // Tenta buscar o nome da pessoa para não mostrar apenas o ID
            return \App\Models\SpecializedEducationalSupport\Person::find($value)?->name ?? "ID: $value";
        }

        if ($field === 'entry_date' && $value) {
            return \Carbon\Carbon::parse($value)->format('d/m/Y');
        }

        return null; // Se retornar null, o sistema usa o valor original
    }


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
        ->withPivot(['academic_year', 'is_current'])
        ->withTimestamps();
    }

    // Curso atual do aluno
    public function currentCourse()
    {
        return $this->hasOne(StudentCourse::class)
            ->where('is_current', true)
            ->with('course');
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
