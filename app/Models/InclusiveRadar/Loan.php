<?php

namespace App\Models\InclusiveRadar;

use App\Enums\InclusiveRadar\LoanStatus;
use App\Models\SpecializedEducationalSupport\Professional;
use App\Models\SpecializedEducationalSupport\Student;
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

    // QUERY SCOPES - Loan

    public function scopeByStatus($query, ?LoanStatus $status)
    {
        if (!is_null($status)) {
            $query->where('status', $status);
        }
        return $query;
    }

    public function scopeByStudent($query, ?int $studentId)
    {
        if (!is_null($studentId)) {
            $query->where('student_id', $studentId);
        }
        return $query;
    }

    public function scopeByProfessional($query, ?int $professionalId)
    {
        if (!is_null($professionalId)) {
            $query->where('professional_id', $professionalId);
        }
        return $query;
    }

    public function scopeLoanedBetween($query, ?string $startDate, ?string $endDate)
    {
        if ($startDate) {
            $query->where('loan_date', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('loan_date', '<=', $endDate);
        }
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
