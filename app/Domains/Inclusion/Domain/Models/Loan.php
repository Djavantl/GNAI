<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Domain\Models;

use App\Domains\Audit\Domain\Contracts\Auditable;
use App\Domains\Audit\Domain\ValueObjects\AuditedField;
use App\Domains\Audit\Infrastructure\Eloquent\AuditsChanges;
use App\Domains\Auth\Domain\Models\User;
use App\Domains\Inclusion\Domain\DTOs\Loans\CreateLoanDTO;
use App\Domains\Inclusion\Domain\DTOs\Loans\ReturnLoanDTO;
use App\Domains\Inclusion\Domain\DTOs\Loans\UpdateLoanDTO;
use App\Domains\Inclusion\Domain\Enums\ItemType;
use App\Domains\Inclusion\Domain\Enums\LoanStatus;
use App\Domains\Inclusion\Domain\Exceptions\InvalidLoan;
use App\Domains\Inclusion\Domain\Exceptions\InvalidLoanableResource;
use Database\Factories\Domains\Inclusion\LoanFactory;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[UseFactory(LoanFactory::class)]
final class Loan extends Model implements Auditable
{
    use AuditsChanges;
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
        'loan_date' => 'datetime',
        'due_date' => 'datetime',
        'return_date' => 'datetime',
        'status' => LoanStatus::class,
    ];

    public static function auditType(): string
    {
        return 'loan';
    }

    public static function auditLabel(): string
    {
        return 'Empréstimo';
    }

    public static function auditedFields(): array
    {
        return [
            'loanable_type' => AuditedField::enum('Tipo de Recurso', valueLabels: [
                'item' => 'Item',
                'assistive_technology' => 'Tecnologia Assistiva',
                'accessible_educational_material' => 'Material Pedagógico Acessível',
            ]),
            // Polimórfico: sem uma coluna de nome comum, fica o código do item (o nome vai na descrição).
            'loanable_id' => AuditedField::number('Código do Item Emprestado'),
            'student_id' => AuditedField::reference('Estudante (Beneficiário)', Student::class, 'person.name'),
            'professional_id' => AuditedField::reference('Profissional (Beneficiário)', Professional::class, 'person.name'),
            'user_id' => AuditedField::reference('Usuário Responsável', User::class),
            'loan_date' => AuditedField::dateTime('Data de Saída'),
            'due_date' => AuditedField::dateTime('Previsão de Devolução'),
            'return_date' => AuditedField::dateTime('Data Real da Devolução'),
            'status' => AuditedField::enum('Status do Empréstimo', LoanStatus::class),
            'observation' => AuditedField::text('Observações'),
        ];
    }

    public function auditDescription(): string
    {
        $itemName = $this->loanable?->name;

        return $itemName === null ? "o empréstimo #{$this->id}" : "o empréstimo #{$this->id} ({$itemName})";
    }

    public static function register(CreateLoanDTO $data): self
    {
        return new self([
            'loanable_id' => $data->loanableId,
            'loanable_type' => $data->loanableType,
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

    /**
     * @throws InvalidLoan
     */
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

    public function isOverdue(): bool
    {
        return $this->status === LoanStatus::ACTIVE
            && $this->due_date !== null
            && $this->due_date->isPast();
    }

    public function isActive(): bool
    {
        return $this->status === LoanStatus::ACTIVE;
    }

    public function isReturned(): bool
    {
        return $this->return_date !== null
            || $this->status?->isReturned() === true;
    }

    public function statusLabel(): string
    {
        return $this->isOverdue()
            ? 'Em Atraso'
            : ($this->status?->label() ?? 'Status desconhecido');
    }

    public function statusColor(): string
    {
        return $this->isOverdue()
            ? 'danger'
            : ($this->status?->color() ?? 'secondary');
    }

    /**
     * @throws InvalidLoanableResource
     */
    public function loanableType(): ItemType
    {
        $type = $this->loanable?->item_type;

        if (! $type instanceof ItemType) {
            throw new InvalidLoanableResource('O item emprestado não foi encontrado.');
        }

        return $type;
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
