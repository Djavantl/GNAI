<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Domain\Models;

use App\Domains\InclusiveRadar\Domain\DTOs\Loans\CreateLoanDTO;
use App\Domains\InclusiveRadar\Domain\DTOs\Loans\ReturnLoanDTO;
use App\Domains\InclusiveRadar\Domain\DTOs\Loans\UpdateLoanDTO;
use App\Domains\InclusiveRadar\Domain\Enums\LoanStatus;
use App\Domains\InclusiveRadar\Domain\Exceptions\InvalidLoan;
use App\Models\SpecializedEducationalSupport\Professional;
use App\Models\SpecializedEducationalSupport\Student;
use App\Models\User;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

final class Loan extends Model
{
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
        'loan_date' => 'datetime',
        'due_date' => 'datetime',
        'return_date' => 'datetime',
        'status' => LoanStatus::class,
    ];

    public static function register(CreateLoanDTO $data): self
    {
        return new self([
            'loanable_id' => $data->loanableId,
            'loanable_type' => $data->loanableType->value,
            'student_id' => $data->studentId,
            'professional_id' => $data->professionalId,
            'user_id' => $data->registeredBy,
            'loan_date' => $data->loanDate,
            'due_date' => $data->dueDate,
            'return_date' => null,
            'status' => LoanStatus::ACTIVE,
            'observation' => $data->observation,
        ]);
    }

    public function revise(UpdateLoanDTO $data): void
    {
        $this->fill([
            'observation' => $data->observation,
        ]);
    }

    public function registerReturn(ReturnLoanDTO $data): void
    {
        if ($this->return_date !== null) {
            throw new InvalidLoan('Este empréstimo já foi finalizado.');
        }

        $this->fill([
            'return_date' => $data->returnDate,
            'status' => $data->status,
            'observation' => $data->observation ?? $this->observation,
        ]);
    }

    public static function returnedStatus(bool $isDamaged, DateTimeInterface $returnDate, DateTimeInterface $dueDate): LoanStatus
    {
        if ($isDamaged) {
            return LoanStatus::DAMAGED;
        }

        return $returnDate > $dueDate
            ? LoanStatus::LATE
            : LoanStatus::RETURNED;
    }

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
}
