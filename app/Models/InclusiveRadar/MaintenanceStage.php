<?php

namespace App\Models\InclusiveRadar;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceStage extends Model
{
    use HasFactory;

    protected $fillable = [
        'maintenance_id',
        'step_number',
        'started_by_user_id',
        'user_id',
        'estimated_cost',
        'real_cost',
        'observation',
        'damage_description',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    /**
     * Usuário que iniciou a etapa (apenas visualização)
     */
    public function starter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'started_by_user_id');
    }

    /**
     * Usuário que concluiu a etapa
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Manutenção à qual esta etapa pertence
     */
    public function maintenance(): BelongsTo
    {
        return $this->belongsTo(Maintenance::class);
    }

    protected function setRealCostAttribute($value)
    {
        if (is_string($value)) {
            // Remove 'R$', espaços e pontos, troca vírgula por ponto
            $this->attributes['real_cost'] = (float) str_replace(',', '.', str_replace('.', '', str_replace('R$', '', $value)));
        } else {
            $this->attributes['real_cost'] = $value;
        }
    }

    protected function setEstimatedCostAttribute($value)
    {
        if (is_string($value)) {
            $this->attributes['estimated_cost'] = (float) str_replace(',', '.', str_replace('.', '', str_replace('R$', '', $value)));
        } else {
            $this->attributes['estimated_cost'] = $value;
        }
    }
}
