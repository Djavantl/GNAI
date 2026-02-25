<?php

namespace App\Models\InclusiveRadar;

use App\Enums\InclusiveRadar\BarrierStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BarrierStage extends Model
{
    use HasFactory;

    protected $fillable = [
        'barrier_id',
        'step_number',
        'status',
        'started_by_user_id',
        'user_id',
        'validator_id',
        'observation',
        'completed_at',
        'analyst_notes',
        'justificativa_encerramento',
        'action_plan_description',
        'intervention_start_date',
        'estimated_completion_date',
        'estimated_cost',
        'actual_cost',
        'resolution_date',
        'delay_justification',
        'resolution_summary',
        'effectiveness_level',
        'maintenance_instructions',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'intervention_start_date' => 'date',
        'estimated_completion_date' => 'date',
        'resolution_date' => 'datetime',
        'status' => BarrierStatus::class,
    ];

    /** -------------------------
     * RELACIONAMENTOS
     * ------------------------- */

    public function barrier(): BelongsTo
    {
        return $this->belongsTo(Barrier::class);
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

    /** -------------------------
     * MÉTODOS ÚTEIS
     * ------------------------- */

    public function isNotApplicable(): bool
    {
        return $this->status === BarrierStatus::NOT_APPLICABLE;
    }

    public function isCompleted(): bool
    {
        return !is_null($this->completed_at);
    }

    public function isResolved(): bool
    {
        return $this->status === BarrierStatus::RESOLVED;
    }
}
