<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Domain\Models;

use App\Domains\Audit\Domain\Contracts\Auditable;
use App\Domains\Audit\Domain\ValueObjects\AuditedField;
use App\Domains\Audit\Infrastructure\Eloquent\AuditsChanges;
use App\Domains\Auth\Domain\Models\User;
use App\Domains\Inclusion\Domain\DTOs\Inspections\CreateInspectionDTO;
use App\Domains\Inclusion\Domain\Enums\BarrierStatus;
use App\Domains\Inclusion\Domain\Enums\ConservationState;
use App\Domains\Inclusion\Domain\Enums\InspectionType;
use Database\Factories\Domains\Inclusion\InspectionFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[UseFactory(InspectionFactory::class)]
final class Inspection extends Model implements Auditable
{
    use AuditsChanges;
    use HasFactory;

    protected $fillable = [
        'inspectable_id',
        'inspectable_type',
        'state',
        'status',
        'inspection_date',
        'description',
        'type',
        'user_id',
    ];

    protected $casts = [
        'inspection_date' => 'date',
        'state' => ConservationState::class,
        'status' => BarrierStatus::class,
        'type' => InspectionType::class,
    ];

    public static function auditType(): string
    {
        return 'inspection';
    }

    public static function auditLabel(): string
    {
        return 'Inspeção';
    }

    public static function auditedFields(): array
    {
        return [
            'inspectable_type' => AuditedField::enum('Tipo do Item Inspecionado', valueLabels: [
                'item' => 'Item',
                'assistive_technology' => 'Tecnologia Assistiva',
                'accessible_educational_material' => 'Material Pedagógico Acessível',
                'barrier' => 'Barreira',
            ]),
            // Polimórfico: sem uma coluna de nome comum, fica o código do item.
            'inspectable_id' => AuditedField::number('Código do Item Inspecionado'),
            'type' => AuditedField::enum('Tipo de Inspeção', InspectionType::class),
            'inspection_date' => AuditedField::date('Data da Inspeção'),
            'state' => AuditedField::enum('Estado de Conservação', ConservationState::class),
            'status' => AuditedField::enum('Status', BarrierStatus::class),
            'description' => AuditedField::richText('Parecer Técnico'),
            'user_id' => AuditedField::reference('Usuário Responsável', User::class),
        ];
    }

    public function auditDescription(): string
    {
        $date = $this->inspection_date?->format('d/m/Y');

        return $date === null ? "a inspeção #{$this->id}" : "a inspeção #{$this->id} de {$date}";
    }

    public static function register(CreateInspectionDTO $data): self
    {
        return new self([
            'state' => $data->state,
            'status' => $data->status,
            'inspection_date' => $data->date,
            'description' => $data->description,
            'type' => $data->type,
            'user_id' => $data->registeredBy,
        ]);
    }

    public function inspectable(): MorphTo
    {
        return $this->morphTo();
    }

    public function evidences(): HasMany
    {
        return $this->hasMany(InspectionEvidence::class, 'inspection_id');
    }

    public function registeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
