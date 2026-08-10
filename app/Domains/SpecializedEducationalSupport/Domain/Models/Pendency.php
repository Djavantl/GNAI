<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Domain\Models;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\SpecializedEducationalSupport\Domain\DTOs\Pendencies\CreatePendencyDTO;
use App\Domains\SpecializedEducationalSupport\Domain\DTOs\Pendencies\UpdatePendencyDTO;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\Priority;
use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidPendency;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

final class Pendency extends Model
{
    use HasFactory;

    protected $table = 'pendencies';

    protected $fillable = [
        'created_by',
        'assigned_to',
        'title',
        'description',
        'priority',
        'due_date',
        'is_completed',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'due_date' => 'date',
        'priority' => Priority::class,
    ];

    public static function register(User $creator, Professional $assignedProfessional, CreatePendencyDTO $data): self
    {
        $assignedProfessional->ensureIsActive();

        return new self([
            'created_by' => $creator->getKey(),
            'assigned_to' => $assignedProfessional->getKey(),
            'title' => trim($data->title),
            'description' => self::normalizeDescription($data->description),
            'priority' => $data->priority->value,
            'due_date' => self::normalizeDueDate($data->dueDate),
            'is_completed' => false,
        ]);
    }

    public function revise(Professional $assignedProfessional, UpdatePendencyDTO $data): void
    {
        $assignedProfessional->ensureIsActive();

        $this->fill([
            'assigned_to' => $assignedProfessional->getKey(),
            'title' => trim($data->title),
            'description' => self::normalizeDescription($data->description),
            'priority' => $data->priority->value,
            'due_date' => self::normalizeDueDate($data->dueDate),
        ]);
    }

    /**
     * @throws InvalidPendency
     */
    public function ensureCanBeEditedBy(User $user): void
    {
        if ((int) $this->created_by !== (int) $user->getKey()) {
            throw new InvalidPendency('Somente o criador pode editar a pendência.');
        }

        if ($this->is_completed) {
            throw new InvalidPendency('Não é possível editar uma pendência já concluída.');
        }
    }

    /**
     * @throws InvalidPendency
     */
    public function ensureCanBeCompletedBy(User $user): void
    {
        $professionalId = $user->professional_id;

        if ($professionalId === null || (int) $this->assigned_to !== (int) $professionalId) {
            throw new InvalidPendency('Somente o responsável pode concluir a pendência.');
        }

        if ($this->is_completed) {
            throw new InvalidPendency('Esta pendência já foi concluída.');
        }
    }

    public function canBeCompletedByCurrentUser(): bool
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            return false;
        }

        return $user->professional_id !== null
            && (int) $this->assigned_to === (int) $user->professional_id
            && ! $this->is_completed;
    }

    public function markAsCompleted(): void
    {
        $this->forceFill([
            'is_completed' => true,
        ]);
    }

    public function getCreatedAtFormattedAttribute(): string
    {
        return $this->created_at
            ? $this->created_at->format('d/m/Y H:i')
            : '—';
    }

    public function getUpdatedAtFormattedAttribute(): string
    {
        return $this->updated_at
            ? $this->updated_at->format('d/m/Y H:i')
            : '—';
    }

    public function getDueDateFormattedAttribute(): string
    {
        return $this->due_date
            ? $this->due_date->format('d/m/Y')
            : '—';
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignedProfessional(): BelongsTo
    {
        return $this->belongsTo(Professional::class, 'assigned_to');
    }

    private static function normalizeDescription(?string $description): ?string
    {
        $normalized = $description !== null ? trim($description) : null;

        return $normalized !== '' ? $normalized : null;
    }

    private static function normalizeDueDate(?string $dueDate): ?string
    {
        return $dueDate !== null
            ? CarbonImmutable::parse($dueDate)->toDateString()
            : null;
    }
}
