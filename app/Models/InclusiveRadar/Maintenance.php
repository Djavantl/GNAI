<?php

namespace App\Models\InclusiveRadar;

use App\Enums\InclusiveRadar\InspectionType;
use App\Enums\InclusiveRadar\MaintenanceStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Maintenance extends Model
{
    use HasFactory;

    protected $fillable = [
        'maintainable_id',
        'maintainable_type',
        'status',
    ];

    protected $casts = [
        'status' => MaintenanceStatus::class,
    ];

    /**
     * Relação polimórfica com o recurso (TA/MPA/etc)
     */
    public function maintainable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Etapas da manutenção
     */
    public function stages(): HasMany
    {
        return $this->hasMany(MaintenanceStage::class);
    }

    /**
     * Retorna a etapa inicial (1)
     */
    public function initialStage()
    {
        return $this->stages()->where('step_number', 1)->first();
    }

    /**
     * Retorna a etapa final (2)
     */
    public function finalStage()
    {
        return $this->stages()->where('step_number', 2)->first();
    }

    /**
     * Inspeções relacionadas à manutenção (polimórfico)
     */
    public function inspection()
    {
        return $this->hasOne(Inspection::class, 'inspectable_id', 'maintainable_id')
            ->where('inspectable_type', $this->maintainable_type)
            ->where('type', InspectionType::MAINTENANCE)
            ->latestOfMany();
    }

    public function scopePending($query)
    {
        return $query->where('status', MaintenanceStatus::PENDING->value);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', MaintenanceStatus::COMPLETED->value);
    }
}
