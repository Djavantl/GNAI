<?php

namespace App\Models\SpecializedEducationalSupport;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;

class PeiDiscipline extends Model
{
    use HasFactory;

    protected $fillable = [
        'pei_id',
        'creator_id',
        'teacher_id',
        'course_id',
        'discipline_id',
        'specific_objectives',
        'content_programmatic',
        'methodologies',
        'evaluations',
    ];

    /**
     * Relacionamentos Principais
     */
    public function pei(): BelongsTo
    {
        return $this->belongsTo(Pei::class, 'pei_id');
    }
    
    public function discipline(): BelongsTo
    {
        return $this->belongsTo(Discipline::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

       public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function getTeacherDisplayNameAttribute(): string
    {   
        return $this->teacher->person->name ?? 'Professor s/ Nome';
    }

        public function getCreatorNameAttribute(): string
    {
        if ($this->creator->is_admin) {
            return 'admin'; 
        } elseif ($this->creator->name) {
            return $this->creator->name; 
        } 

        return 'Sistema/Desconhecido';
    }

    public function scopeDiscipline($query, ?int $disciplineId)
    {
        if (!$disciplineId) return $query;

        return $query->where('discipline_id', $disciplineId);
    }


    public function scopeVisibleToUser($query, $user)
    {
        // só aplica a regra se for professor
        if (!$user->teacher_id) {
            return $query;
        }

        return $query->whereIn('discipline_id', function ($q) use ($user) {
            $q->select('discipline_id')
            ->from('discipline_teacher')
            ->where('teacher_id', $user->teacher_id);
        });
    }
}