<?php

namespace App\Models\InclusiveRadar;

use App\Enums\InclusiveRadar\BarrierStatus;
use App\Enums\InclusiveRadar\EffectivenessLevel;
use App\Enums\Priority;
use App\Models\SpecializedEducationalSupport\Deficiency;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Barrier extends Model
{
    use HasFactory;

    protected $fillable = [
        // IDENTIDADE
        'step_number',
        'status',

        // DADOS GERAIS (antiga Barrier)
        'name',
        'description',
        'institution_id',
        'barrier_category_id',
        'location_id',

        'affected_student_id',
        'affected_professional_id',
        'affected_person_name',
        'affected_person_role',
        'is_anonymous',

        'priority',
        'identified_at',
        'latitude',
        'longitude',
        'location_specific_details',

        // FLUXO
        'started_by_user_id',
        'user_id',
        'validator_id',
        'observation',
        'completed_at',

        // ANÁLISE
        'analyst_notes',
        'justificativa_encerramento',

        // PLANO DE AÇÃO
        'action_plan_description',
        'intervention_start_date',
        'estimated_completion_date',
        'estimated_cost',

        // RESOLUÇÃO
        'actual_cost',
        'resolution_date',
        'delay_justification',
        'resolution_summary',
        'effectiveness_level',
        'maintenance_instructions',
    ];

    protected $casts = [
        'identified_at' => 'date',
        'completed_at' => 'datetime',
        'intervention_start_date' => 'date',
        'estimated_completion_date' => 'date',
        'resolution_date' => 'datetime',

        'is_anonymous' => 'boolean',

        'priority' => Priority::class,
        'status' => BarrierStatus::class,
        'effectiveness_level' => EffectivenessLevel::class,
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONAMENTOS
    |--------------------------------------------------------------------------
    */

    public function inspections(): MorphMany
    {
        return $this->morphMany(Inspection::class, 'inspectable')
            ->with('images')
            ->orderByDesc('inspection_date')
            ->orderByDesc('created_at');
    }

    public function starter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'started_by_user_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validator_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(BarrierCategory::class, 'barrier_category_id');
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class, 'institution_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function deficiencies(): BelongsToMany
    {
        return $this->belongsToMany(
            Deficiency::class,
            'barrier_deficiency'
        )->withTimestamps();
    }

    /*
    |--------------------------------------------------------------------------
    | REGRAS DE ATUALIZAÇÃO
    |--------------------------------------------------------------------------
    */
    protected static function booted()
    {
        static::updating(function (Barrier $barrier) {
            $dirtyFields = array_keys($barrier->getDirty());

            // 1. Campos imutáveis após a criação
            $blockedFields = [
                'name', 'institution_id', 'location_id', 'latitude', 'longitude',
                'location_specific_details', 'affected_student_id', 'affected_professional_id',
                'affected_person_name', 'affected_person_role', 'identified_at',
            ];

            foreach ($dirtyFields as $field) {
                if (in_array($field, $blockedFields)) {
                    throw new \Exception("O campo {$field} não pode ser alterado após a criação.");
                }
            }

            // 2. Regra dos campos editáveis apenas no Step 2
            $allowedOnlyInStep2 = ['description', 'barrier_category_id', 'priority'];

            // Pegamos o step_number que está vindo no request de update
            $targetStep = (int) $barrier->step_number;

            foreach ($dirtyFields as $field) {
                if (in_array($field, $allowedOnlyInStep2)) {
                    // Se o campo mudou, mas o step_number NÃO É 2 e NÃO ESTÁ MUDANDO para 2 agora
                    if ($targetStep !== 2) {
                        throw new \Exception(
                            "O campo {$field} só pode ser alterado durante a etapa 2."
                        );
                    }
                }
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */
    public function scopeName($query, $name = null)
    {
        return $query->when($name, function ($q) use ($name) {
            $q->where('name', 'like', "%{$name}%");
        });
    }

    public function scopeCategory($query, $category = null)
    {
        return $query->when($category, function ($q) use ($category) {
            $q->where('barrier_category_id', $category);
        });
    }

    public function scopePriority($query, $priority = null)
    {
        return $query->when($priority, function ($q) use ($priority) {
            $q->where('priority', $priority);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTODOS AUXILIARES
    |--------------------------------------------------------------------------
    */
    public function latestInspection(): ?Inspection
    {
        return $this->inspections->first();
    }

    public function currentStatus(): ?\App\Enums\InclusiveRadar\BarrierStatus
    {
        return $this->latestInspection()?->status ?? $this->status;
    }

    public function nextStep(): ?int
    {
        if ($this->status === BarrierStatus::NOT_APPLICABLE) {
            return null;
        }

        if ($this->step_number === 1) {
            return 2;
        }

        if ($this->step_number === 2) {
            return 3;
        }

        if ($this->step_number === 3) {
            return 4;
        }

        return null;
    }

    public function isClosedOrNotApplicable(): bool
    {
        return in_array(
            $this->currentStatus()?->value,
            ['resolved', 'not_applicable']
        );
    }
}
