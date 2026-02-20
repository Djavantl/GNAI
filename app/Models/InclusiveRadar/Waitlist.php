<?php

namespace App\Models\InclusiveRadar;

use App\Models\SpecializedEducationalSupport\Student;
use App\Models\SpecializedEducationalSupport\Professional;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Waitlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'waitlistable_id',
        'waitlistable_type',
        'student_id',
        'professional_id',
        'user_id',
        'requested_at',
        'status',
        'observation',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];

    // ------------------------------------------------------
    // RELATIONSHIPS
    // ------------------------------------------------------

    public function waitlistable(): MorphTo
    {
        return $this->morphTo();
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function professional(): BelongsTo
    {
        return $this->belongsTo(Professional::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ------------------------------------------------------
    // SCOPES DE FILTRO
    // ------------------------------------------------------

    /**
     * Filtra pelo nome do estudante
     */
    public function scopeStudent($query, $name = null)
    {
        if (!$name) return $query;

        return $query->whereHas('student.person', function ($q) use ($name) {
            $q->where('name', 'like', "%$name%");
        });
    }

    /**
     * Filtra pelo nome do profissional
     */
    public function scopeProfessional($query, $name = null)
    {
        if (!$name) return $query;

        return $query->whereHas('professional.person', function ($q) use ($name) {
            $q->where('name', 'like', "%$name%");
        });
    }
}
