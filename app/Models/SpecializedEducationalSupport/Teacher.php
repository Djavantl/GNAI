<?php

namespace App\Models\SpecializedEducationalSupport;

use Illuminate\Database\Eloquent\Model;
use App\Models\SpecializedEducationalSupport\Person;
use App\Domains\Auth\Domain\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Traits\Reportable;


class Teacher extends Model
{
    use Reportable;

    protected $fillable = ['person_id', 'registration'];

     public static function getEmbeddedRelations(): array
    {
        return ['person'];
    }


    public static function getReportLabel(): string
    {
        return 'Professores';
    }

    public static function getReportColumns(): ?array
    {
        return ['person.name', 'registration', 'person.email', 'person.document', 'person.birth_date', 'person.gender', 'person.phone', 'person.address'];
    }


    public static function getReportColumnLabels(): array
    {
        return [
            'registration' => 'Matrícula',
            'person.name'  => 'Nome do Professor',
            'person.email' => 'E-mail',
            'person.document'=> 'CPF',
            'person.birth_date'=> 'Data de Nascimento',
            'person.gender'=> 'Gênero',
            'person.phone'=> 'Telefone',
            'person.address'=> 'Endereço',
        ];
    }

    public function person() {
        return $this->belongsTo(Person::class);
    }

    public function user()
    {
        return $this->hasOne(User::class, 'teacher_id');
    }

    public function disciplines()
    {
        // Usando a tabela teacher_course_disciplines como pivô
        return $this->belongsToMany(Discipline::class, 'teacher_course_disciplines', 'teacher_id', 'discipline_id');
    }

    public function courseDisciplines()
    {
        return $this->hasMany(TeacherCourseDiscipline::class);
    }
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'teacher_courses')->withTimestamps();
    }

    public function peiDisciplines()
    {
        return $this->hasMany(PeiDiscipline::class, 'teacher_id');
    }

    public function scopeName($query, ?string $term)
    {
        if (!$term) return $query;

        return $query->whereHas('person', function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%");
        });
    }

    public function scopeEmail($query, ?string $term)
    {
        if (!$term) return $query;

        return $query->whereHas('person', function ($q) use ($term) {
            $q->where('email', 'like', "%{$term}%");
        });
    }

    public function scopeRegistration($query, ?string $term)
    {
        if (!$term) return $query;

        return $query->where('registration', 'like', "%{$term}%");
    }
}