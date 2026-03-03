<?php

namespace App\Models\InclusiveRadar;

use App\Models\AuditLog;
use App\Models\SpecializedEducationalSupport\Deficiency;
use App\Enums\InclusiveRadar\ConservationState;
use App\Enums\InclusiveRadar\ResourceStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\Auditable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class AssistiveTechnology extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $table = 'assistive_technologies';

    protected $fillable = [
        'name',
        'is_digital',
        'notes',
        'asset_code',
        'quantity',
        'quantity_available',
        'conservation_state',
        'is_loanable',
        'status',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_digital' => 'boolean',
        'is_loanable' => 'boolean',
        'conservation_state' => ConservationState::class,
        'status' => ResourceStatus::class,
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONAMENTOS
    |--------------------------------------------------------------------------
    */

    public function deficiencies(): BelongsToMany
    {
        return $this->belongsToMany(
            Deficiency::class,
            'assistive_technology_deficiency',
            'assistive_technology_id',
            'deficiency_id'
        );
    }

    public function trainings(): MorphMany {
        return $this->morphMany(Training::class, 'trainable');
    }

    public function inspections(): MorphMany
    {
        return $this->morphMany(Inspection::class, 'inspectable')
            ->with('images')
            ->orderByDesc('inspection_date')
            ->orderByDesc('created_at');
    }

    public function loans(): MorphMany
    {
        return $this->morphMany(Loan::class, 'loanable');
    }

    public function waitlists(): MorphMany
    {
        return $this->morphMany(Waitlist::class, 'waitlistable');
    }

    public function logs(): MorphMany
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeFilterName(Builder $query, ?string $name): Builder
    {
        return $name
            ? $query->where('name', 'like', "%{$name}%")
            : $query;
    }

    public function scopeActive(Builder $query, $isActive): Builder
    {
        if (!is_null($isActive) && $isActive !== '') {
            $query->where('is_active', $isActive == '1');
        }
        return $query;
    }

    public function scopeAvailable(Builder $query, $available): Builder
    {
        if (!is_null($available) && $available !== '') {
            $available == '1'
                ? $query->where('quantity_available', '>', 0)
                : $query->where('quantity_available', '<=', 0);
        }
        return $query;
    }

    public function scopeDigital(Builder $query, $isDigital): Builder
    {
        if (!is_null($isDigital) && $isDigital !== '') {
            $query->where('is_digital', $isDigital == '1');
        }
        return $query;
    }

    /*
    |--------------------------------------------------------------------------
    | AUDITORIA
    |--------------------------------------------------------------------------
    */

    public static function getAuditLabels(): array
    {
        return [
            'name' => 'Tipo da Tecnologia',
            'is_digital' => 'Tecnologia Digital',
            'notes' => 'Descrição',
            'asset_code' => 'Código de Patrimônio',
            'quantity' => 'Quantidade Total',
            'conservation_state' => 'Estado de Conservação',
            'status' => 'Status do Recurso',
            'is_active' => 'Cadastro Ativo',
            'deficiencies' => 'Público-Alvo (Deficiências)',
            'trainings' => 'Treinamentos',
        ];
    }

    public static function formatAuditValue(string $field, $value): ?string
    {
        if ($field === 'deficiencies' && is_array($value)) {
            return Deficiency::whereIn('id', $value)
                ->pluck('name')
                ->join(', ') ?: 'Nenhuma';
        }

        if ($field === 'trainings' && is_array($value)) {
            return Training::whereIn('id', $value)
                ->pluck('title')
                ->join(', ') ?: 'Nenhum';
        }

        if ($field === 'is_digital') {
            return $value ? 'Digital' : 'Físico';
        }

        if ($field === 'status' && $value) {
            return ResourceStatus::from($value)->label();
        }

        if ($field === 'conservation_state' && $value) {
            return ConservationState::from($value)->label();
        }

        return null;
    }
}
