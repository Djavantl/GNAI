<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Domain\Models;

use App\Domains\InclusiveRadar\Domain\DTOs\Waitlists\CreateWaitlistDTO;
use App\Domains\InclusiveRadar\Domain\DTOs\Waitlists\UpdateWaitlistDTO;
use App\Domains\InclusiveRadar\Domain\Enums\WaitlistStatus;
use App\Domains\InclusiveRadar\Domain\Exceptions\InvalidWaitlist;
use App\Models\SpecializedEducationalSupport\Professional;
use App\Models\SpecializedEducationalSupport\Student;
use App\Models\User;
use Database\Factories\Domains\InclusiveRadar\WaitlistFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[UseFactory(WaitlistFactory::class)]
final class Waitlist extends Model
{
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

    public static function register(CreateWaitlistDTO $data): self
    {
        return new self([
            'waitlistable_id' => $data->waitlistableId,
            'waitlistable_type' => $data->waitlistableType->value,
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
