<?php

namespace App\Models\InclusiveRadar;

use App\Enums\InclusiveRadar\LoanStatus;
use App\Models\SpecializedEducationalSupport\Professional;
use App\Models\SpecializedEducationalSupport\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Loan extends Model
{
    use HasFactory;

    protected $table = 'loans';

    protected $fillable = [
        'loanable_id',
        'loanable_type',
        'student_id',
        'professional_id',
        'user_id',
        'loan_date',
        'due_date',
        'return_date',
        'status',
        'observation',
    ];

    protected $casts = [
        'loan_date'   => 'datetime',
        'due_date'    => 'datetime',
        'return_date' => 'datetime',
        'status'      => LoanStatus::class,
    ];

    // ------------------------------------------------------
    // RELATIONSHIPS
    // ------------------------------------------------------

    public function loanable(): MorphTo
    {
        return $this->morphTo()->withTrashed();
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function professional(): BelongsTo
    {
        return $this->belongsTo(Professional::class, 'professional_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ------------------------------------------------------
    // QUERY SCOPES
    // ------------------------------------------------------

    public function scopeByStatus($query, ?LoanStatus $status)
    {
        if (!is_null($status)) {
            $query->where('status', $status);
        }
        return $query;
    }

    /**
     * Filtra pelo nome do estudante
     */
    public function scopeStudent($query, ?string $name)
    {
        if (!$name) return $query;

        return $query->whereHas('student.person', function ($q) use ($name) {
            $q->where('name', 'like', "%$name%");
        });
    }

    /**
     * Filtra pelo nome do profissional
     */
    public function scopeProfessional($query, ?string $name)
    {
        if (!$name) return $query;

        return $query->whereHas('professional.person', function ($q) use ($name) {
            $q->where('name', 'like', "%$name%");
        });
    }

    public function scopeItem($query, ?string $name)
    {
        if (!$name) return $query;

        $name = strtolower($name);

        $query->where(function ($q) use ($name) {

            $q->where('loanable_type', AssistiveTechnology::class)
                ->whereHas('loanable', function ($q2) use ($name) {
                    $q2->whereRaw('LOWER(name) LIKE ?', ["%$name%"]);
                });


            $q->orWhere(function ($q2) use ($name) {
                $q2->where('loanable_type', AccessibleEducationalMaterial::class)
                    ->whereHas('loanable', function ($q3) use ($name) {
                        $q3->whereRaw('LOWER(name) LIKE ?', ["%$name%"]);
                    });
            });

        });

        return $query;
    }

    public function scopeByUser($query, ?int $userId)
    {
        if (!is_null($userId)) {
            $query->where('user_id', $userId);
        }
        return $query;
    }

    public function scopeLoanedBetween($query, ?string $startDate, ?string $endDate)
    {
        if ($startDate) $query->where('loan_date', '>=', $startDate);
        if ($endDate)   $query->where('loan_date', '<=', $endDate);
        return $query;
    }

    public function scopeActive($query)
    {
        return $query->where('status', LoanStatus::ACTIVE);
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', LoanStatus::ACTIVE)
            ->where('due_date', '<', now());
    }

    public function scopeReturned($query)
    {
        return $query->where('status', LoanStatus::RETURNED);
    }
}
