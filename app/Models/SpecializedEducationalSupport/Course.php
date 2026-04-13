<?php

namespace App\Models\SpecializedEducationalSupport;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Reportable;
use DomainException;

class Course extends Model
{
    use HasFactory;
    use Reportable;

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    public static function getReportLabel(): string
    {
        return 'Cursos';
    }

    public static function getReportColumns(): array
    {
        return [
            'name',
            'description',
            'is_active',
        ];
    }


    public static function getReportColumnLabels(): array
    {
        return [
            'name' => 'Nome',
            'description' => 'Descrição',
            'is_active' => 'Ativo',
        ];
    }

    public function studentCourses()
    {
        return $this->hasMany(StudentCourse::class);
    }
    public function disciplines()
    {
        return $this->belongsToMany(Discipline::class,'course_disciplines')->withTimestamps();
    }

    // Scopes para Filtros Dinâmicos
    public function scopeName($query, ?string $term)
    {
        if (!$term) return $query;
        return $query->where('name', 'like', "%{$term}%");
    }

    public function scopeActive($query, $isActive)
    {
        if ($isActive === null || $isActive === '') return $query;
        return $query->where('is_active', (bool) $isActive);
    }

        public function ensureCanBeDeactivated(): void
    {
        if ($this->studentCourses()->exists()) {
            throw new DomainException(
                'Este curso está vinculado a um ou mais alunos e não pode ser desativado.'
            );
        }

        // Se você tiver vínculo com professor, descomente e ajuste a relação:
        // if ($this->professors()->exists()) {
        //     throw new DomainException(
        //         'Este curso está vinculado a um ou mais professores e não pode ser desativado.'
        //     );
        // }
    }

    public function ensureIsActive(): void
    {
        if (! $this->is_active) {
            throw new DomainException(
                'Este curso está desativado e não pode ser vinculado.'
            );
        }
    }
}

