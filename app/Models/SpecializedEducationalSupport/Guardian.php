<?php

namespace App\Models\SpecializedEducationalSupport;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Traits\Reportable;

class Guardian extends Model
{
    use HasFactory;
     use Reportable;

    protected $table = 'student_guardians';

    protected $fillable = [
        'student_id',
        'person_id',
        'relationship',
    ];

    public static function genderOptions(): array
    {
        return [
            'male' => 'Masculino',
            'female' => 'Feminino',
            'other' => 'Outro',
            'not_specified' => 'Não informado',
        ];
    }

     public static function getEmbeddedRelations(): array
    {
        return ['person'];
    }


    public static function getReportLabel(): string
    {
        return 'Responsáveis';
    }

    public static function getReportColumns(): ?array
    {
        return ['person.name', 'relationship', 'person.email', 'person.document', 'person.birth_date', 'person.gender', 'person.phone', 'person.address'];
    }


    public static function getReportColumnLabels(): array
    {
        return [
            'person.name'  => 'Nome do Responsável',
            'relationship'  => 'Relação com aluno',
            'person.email' => 'E-mail',
            'person.document'=> 'CPF',
            'person.birth_date'=> 'Data de Nascimento',
            'person.gender'=> 'Gênero',
            'person.phone'=> 'Telefone',
            'person.address'=> 'Endereço',
        ];
    }
    
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    // Scopes para Filtros
    public function scopeName($query, ?string $name)
    {
        if (!$name) return $query;
        return $query->whereHas('person', function($q) use ($name) {
            $q->where('name', 'like', "{$name}%");
        });
    }

    public function scopeEmail($query, ?string $email)
    {
        if (!$email) return $query;
        return $query->whereHas('person', function($q) use ($email) {
            $q->where('email', 'like', "%{$email}%");
        });
    }

    public function scopeRelationship($query, ?string $relationship)
    {
        if (!$relationship) return $query;
        return $query->where('relationship', 'like', "%{$relationship}%");
    }
}
