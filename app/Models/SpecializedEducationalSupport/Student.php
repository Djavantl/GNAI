<?php

namespace App\Models\SpecializedEducationalSupport;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Reportable;
use App\Models\Traits\Auditable; 
use App\Models\AuditLog;       
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Builder;

class Student extends Model
{
    use Reportable;

    protected $fillable = [
        'person_id',
        'registration',
        'entry_date',
        'status',
    ];


    /*
    |--------------------------------------------------------------------------
    | Auditoria
    |--------------------------------------------------------------------------
    */

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
            'entry_date'      => 'Data de Ingresso',
            'status'          => 'Status Acadêmico',
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
            return \App\Models\SpecializedEducationalSupport\Person::find($value)?->name ?? "ID: $value";
        }

        if ($field === 'entry_date' && $value) {
            return \Carbon\Carbon::parse($value)->format('d/m/Y');
        }

        return null;
    }


    /*
    |--------------------------------------------------------------------------
    | Configuração do Report Builder
    |--------------------------------------------------------------------------
    */

    public static function getEmbeddedRelations(): array
    {
        return ['person'];
    }

    public static function getReportColumns(): ?array
    {
        return ['person.name', 'registration', 'status', 'entry_date', 'person.email', 'person.document', 'person.birth_date', 'person.gender', 'person.phone', 'person.address'];
    }

    public static function getReportColumnLabels(): array
    {
        return [
            'registration' => 'Matrícula',
            'person.name'  => 'Nome do Aluno',
            'entry_date'   => 'Data de Ingresso',
            'person.email' => 'E-mail',
            'person.document'=> 'CPF',
            'person.birth_date'=> 'Data de Nascimento',
            'person.gender'=> 'Gênero',
            'person.phone'=> 'Telefone',
            'person.address'=> 'Endereço',
        ];
    }

    public static function getReportLabel()
    {
        return 'Alunos';
    }


    /*
    |--------------------------------------------------------------------------
    | Relacionamentos
    |--------------------------------------------------------------------------
    */

    /**
     * Pessoa
     */
    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    /**
     * Responsáveis
     */
    public function guardians()
    {
        return $this->hasMany(Guardian::class);
    }

    /**
     * Documentos
     */
    public function documents()
    {
        return $this->hasMany(StudentDocument::class);
    }


    /*
    |--------------------------------------------------------------------------
    | Contexto Educacional
    |--------------------------------------------------------------------------
    */

    public function contexts()
    {
        return $this->hasMany(StudentContext::class);
    }

    public function currentContext()
    {
        return $this->hasOne(StudentContext::class)->where('is_current', true);
    }


    /*
    |--------------------------------------------------------------------------
    | Deficiências do Aluno
    |--------------------------------------------------------------------------
    */

    public function deficiencies()
    {
        return $this->belongsToMany(Deficiency::class, 'students_deficiencies')
            ->using(StudentDeficiencies::class)
            ->withPivot([
                'severity',
                'uses_support_resources',
                'notes'
            ])
            ->withTimestamps();
    }


    /*
    |--------------------------------------------------------------------------
    | PEI
    |--------------------------------------------------------------------------
    */

    public function peis()
    {
        return $this->hasMany(Pei::class, 'student_id');
    }


    /*
    |--------------------------------------------------------------------------
    | Cursos
    |--------------------------------------------------------------------------
    */

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

    /**
     * Cursos do aluno
     */
    public function courses()
    {
        return $this->belongsToMany(
            Course::class,
            'student_courses'
        )
        ->withPivot(['academic_year', 'is_current'])
        ->withTimestamps();
    }

    /**
     * Curso atual do aluno
     */
    public function currentCourse()
    {
        return $this->hasOne(StudentCourse::class)
            ->where('is_current', true)
            ->with('course');
    }


    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public static function statusOptions(): array
    {
        return [
            'active'    => 'Ativo',
            'locked'    => 'Trancado',
            'completed' => 'Concluído',
            'dropped'   => 'Evadido',
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Scopes de Busca
    |--------------------------------------------------------------------------
    */

    /**
     * Buscar por nome
     */
    public function scopeName(Builder $query, ?string $term): Builder
    {
        if (!$term) return $query;

        return $query->whereHas('person', fn($q) =>
            $q->where('name', 'like', "{$term}%")
        );
    }

    /**
     * Buscar por matrícula
     */
    public function scopeRegistration(Builder $query, ?string $term): Builder
    {
        if (!$term) return $query;

        return $query->where('registration', 'like', "%{$term}%");
    }

    /**
     * Buscar por email
     */
    public function scopeEmail(Builder $query, ?string $term): Builder
    {
        if (!$term) return $query;

        return $query->whereHas('person', fn($q) =>
            $q->where('email', 'like', "%{$term}%")
        );
    }

    /**
     * Filtrar por status
     */
    public function scopeStatus(Builder $query, ?string $status): Builder
    {
        if (!is_null($status) && $status !== '') {
            $query->where('status', $status);
        }

        return $query;
    }


    /*
    |--------------------------------------------------------------------------
    | Filtros Futuramente Utilizados
    |--------------------------------------------------------------------------
    */

    // public function scopeSemester(Builder $query, $semesterId): Builder
    // {
    //     if (!is_null($semesterId) && $semesterId !== '') {
    //         $query->whereHas('person', fn($q) =>
    //             $q->where('semester_id', $semesterId)
    //         );
    //     }

    //     return $query;
    // }
}