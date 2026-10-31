<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Domain\Models;

use App\Domains\Audit\Domain\Contracts\Auditable;
use App\Domains\Audit\Domain\ValueObjects\AuditedField;
use App\Domains\Audit\Infrastructure\Eloquent\AuditsChanges;
use App\Domains\Auth\Domain\Models\User;
use App\Domains\Inclusion\Domain\DTOs\Waitlists\CreateWaitlistDTO;
use App\Domains\Inclusion\Domain\DTOs\Waitlists\UpdateWaitlistDTO;
use App\Domains\Inclusion\Domain\Enums\WaitlistStatus;
use App\Domains\Inclusion\Domain\Exceptions\InvalidWaitlist;
use Database\Factories\Domains\Inclusion\WaitlistFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[UseFactory(WaitlistFactory::class)]
final class Waitlist extends Model implements Auditable
{
    use AuditsChanges;
    use HasFactory;

    protected $table = 'waitlists';

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
        'status' => WaitlistStatus::class,
    ];

    public static function auditType(): string
    {
        return 'waitlist';
    }

    public static function auditLabel(): string
    {
        return 'Solicitação da Fila de Espera';
    }

    public static function auditedFields(): array
    {
        return [
            'waitlistable_type' => AuditedField::enum('Tipo de Recurso', valueLabels: [
                'item' => 'Item',
                'assistive_technology' => 'Tecnologia Assistiva',
                'accessible_educational_material' => 'Material Pedagógico Acessível',
            ]),
            // Polimórfico: sem uma coluna de nome comum, fica o código do item (o nome vai na descrição).
            'waitlistable_id' => AuditedField::number('Código do Item Solicitado'),
            'student_id' => AuditedField::reference('Aluno (Beneficiário)', Student::class, 'person.name'),
            'professional_id' => AuditedField::reference('Profissional (Beneficiário)', Professional::class, 'person.name'),
            'user_id' => AuditedField::reference('Usuário Responsável pelo Registro', User::class),
            'requested_at' => AuditedField::dateTime('Data da Solicitação'),
            'status' => AuditedField::enum('Status da Solicitação', WaitlistStatus::class),
            'observation' => AuditedField::text('Observações'),
        ];
    }

    public function auditDescription(): string
    {
        $itemName = $this->waitlistable?->name;

        return $itemName === null
            ? "a solicitação #{$this->id} da fila de espera"
            : "a solicitação #{$this->id} da fila de espera ({$itemName})";
    }

    public static function register(CreateWaitlistDTO $data): self
    {
        return new self([
            'waitlistable_id' => $data->waitlistableId,
            'waitlistable_type' => $data->waitlistableType,
            'student_id' => $data->studentId,
            'professional_id' => $data->professionalId,
            'user_id' => $data->registeredBy,
            'requested_at' => $data->requestedAt,
            'status' => WaitlistStatus::WAITING,
            'observation' => $data->observation,
        ]);
    }

    /**
     * @throws InvalidWaitlist
     */
    public function revise(UpdateWaitlistDTO $data): void
    {
        if (
            $data->status !== null
            && in_array($this->status, [WaitlistStatus::FULFILLED, WaitlistStatus::CANCELLED], true)
        ) {
            throw new InvalidWaitlist(
                'Solicitação já finalizada não pode ter o status alterado.'
            );
        }

        $this->fill([
            'status' => $data->status ?? $this->status,
            'observation' => $data->observation,
        ]);
    }

    /**
     * @throws InvalidWaitlist
     */
    public function cancel(): void
    {
        if ($this->status !== WaitlistStatus::WAITING) {
            throw new InvalidWaitlist('Apenas solicitações em espera podem ser canceladas.');
        }

        $this->status = WaitlistStatus::CANCELLED;
    }

    /**
     * @throws InvalidWaitlist
     */
    public function ensureCanBeDeleted(): void
    {
        if ($this->status === WaitlistStatus::FULFILLED) {
            throw new InvalidWaitlist('Solicitações já atendidas não podem ser removidas.');
        }
    }

    public function waitlistable(): MorphTo
    {
        return $this->morphTo()->withTrashed();
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
}
