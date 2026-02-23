<?php

namespace App\Models\SpecializedEducationalSupport;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Guardian extends Model
{
    use HasFactory;

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
