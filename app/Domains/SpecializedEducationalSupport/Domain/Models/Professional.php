<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Domain\Models;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\InclusiveRadar\Domain\Models\Loan;
use App\Domains\InclusiveRadar\Domain\Models\Waitlist;
use App\Domains\SpecializedEducationalSupport\Domain\DTOs\Professionals\CreateProfessionalDTO;
use App\Domains\SpecializedEducationalSupport\Domain\DTOs\Professionals\UpdateProfessionalDTO;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\ProfessionalStatus;
use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidProfessional;
use App\Models\Traits\Reportable;
use Carbon\CarbonImmutable;
use Database\Factories\Domains\SpecializedEducationalSupport\ProfessionalFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[UseFactory(ProfessionalFactory::class)]
final class Professional extends Model
{
    use HasFactory;
    use Reportable;

    protected $table = 'professionals';

    protected $fillable = [
        'person_id',
        'position_id',
        'registration',
        'entry_date',
        'status',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'status' => ProfessionalStatus::class,
    ];

    /**
     * @throws InvalidProfessional
     */
    public static function register(Person $person, Position $position, CreateProfessionalDTO $data): self
    {
        if (! $person->exists || ! $position->exists) {
            throw new InvalidProfessional(
                'O profissional deve ser vinculado a uma pessoa e a um cargo persistidos.'
            );
        }

        $position->ensureIsActive();

        return new self([
            'person_id' => $person->getKey(),
            'position_id' => $position->getKey(),
            'registration' => $data->registration->value(),
            'entry_date' => CarbonImmutable::parse($data->entryDate)->toDateString(),
            'status' => $data->status->value,
        ]);
    }

    public function revise(Position $position, UpdateProfessionalDTO $data): void
    {
        $position->ensureIsActive();

        $this->fill([
            'position_id' => $position->getKey(),
            'registration' => $data->registration->value(),
            'entry_date' => CarbonImmutable::parse($data->entryDate)->toDateString(),
            'status' => $data->status->value,
        ]);
    }

    /**
     * @throws InvalidProfessional
     */
    public function ensureCanBeInactivated(bool $hasPendingPendencies, bool $hasSessions): void
    {
        if ($hasPendingPendencies) {
            throw new InvalidProfessional(
                "O profissional {$this->person->name} possui pendências em aberto e não pode ser inativado."
            );
        }

        if ($hasSessions) {
            throw new InvalidProfessional(
                "O profissional {$this->person->name} possui atendimentos registrados e não pode ser inativado."
            );
        }
    }

    /**
     * @throws InvalidProfessional
     */
    public function ensureCanBeDeleted(bool $hasLinkedRecords): void
    {
        if ($hasLinkedRecords) {
            throw new InvalidProfessional(
                'Este profissional possui registros de atendimento ou acompanhamento vinculados e não pode ser excluído.'
            );
        }
    }

    /**
     * @throws InvalidProfessional
     */
    public function ensureCanBeDeletedBy(?int $actorProfessionalId): void
    {
        if ($actorProfessionalId !== null && $actorProfessionalId === (int) $this->getKey()) {
            throw new InvalidProfessional('Você não pode excluir seu próprio registro de profissional.');
        }
    }

    /**
     * @throws InvalidProfessional
     */
    public function ensureIsActive(): void
    {
        if ($this->status !== ProfessionalStatus::ACTIVE) {
            throw new InvalidProfessional(
                "O profissional {$this->person->name} não está ativo e não pode realizar esta ação."
            );
        }
    }

    public static function getEmbeddedRelations(): array
    {
        return ['person'];
    }

    public static function getReportLabel(): string
    {
        return 'Profissionais';
    }

    public static function getReportColumns(): array
    {
        return [
            'person.name',
            'registration',
            'status',
            'entry_date',
            'person.email',
            'person.document',
            'person.birth_date',
            'person.gender',
            'person.phone',
            'person.address',
        ];
    }

    public static function getReportColumnLabels(): array
    {
        return [
            'registration' => 'Matrícula',
            'person.name' => 'Nome do Profissional',
            'entry_date' => 'Data de Ingresso',
            'person.email' => 'E-mail',
            'person.document' => 'CPF',
            'person.birth_date' => 'Data de Nascimento',
            'person.gender' => 'Gênero',
            'person.phone' => 'Telefone',
            'person.address' => 'Endereço',
        ];
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id');
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'position_id');
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'professional_id');
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class, 'professional_id');
    }

    public function waitlists(): HasMany
    {
        return $this->hasMany(Waitlist::class, 'professional_id');
    }
}
