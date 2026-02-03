<?php

namespace App\Models\SpecializedEducationalSupport;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
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

    // Contexto educacional
    
    public function contexts()
    {
        return $this->hasMany(StudentContext::class);
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
