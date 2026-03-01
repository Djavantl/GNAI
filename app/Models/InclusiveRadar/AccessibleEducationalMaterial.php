<?php

namespace App\Models\InclusiveRadar;

use App\Enums\InclusiveRadar\ConservationState;
use App\Models\AuditLog;
use App\Models\SpecializedEducationalSupport\Deficiency;
use App\Models\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccessibleEducationalMaterial extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $table = 'accessible_educational_materials';

    /*
    |--------------------------------------------------------------------------
    | FILLABLE
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'name',
        'is_digital',
        'notes',
        'asset_code',
        'quantity',
        'quantity_available',
        'conservation_state',
        'status_id',
        'is_active',
    ];

    /*
    |--------------------------------------------------------------------------
    | CASTS
    |--------------------------------------------------------------------------
    */

    protected $casts = [
        'is_active' => 'boolean',
        'is_digital' => 'boolean',
        'conservation_state' => ConservationState::class,
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONAMENTOS
    |--------------------------------------------------------------------------
    */

    public function resourceStatus(): BelongsTo
    {
        return $this->belongsTo(ResourceStatus::class, 'status_id');
    }

    public function deficiencies(): BelongsToMany
    {
        return $this->belongsToMany(
            Deficiency::class,
            'accessible_educational_material_deficiency',
            'accessible_educational_material_id',
            'deficiency_id'
        );
    }

    public function accessibilityFeatures(): BelongsToMany
    {
        return $this->belongsToMany(
            AccessibilityFeature::class,
            'accessible_educational_material_accessibility',
            'accessible_educational_material_id',
            'accessibility_feature_id'
        );
    }

    public function trainings(): BelongsToMany
    {
        return $this->belongsToMany(
            Training::class,
            'accessible_educational_material_training',
            'accessible_educational_material_id',
            'training_id'
        );
    }

    public function inspections(): MorphMany
    {
        return $this->morphMany(Inspection::class, 'inspectable')
            ->with('images')
            ->orderByDesc('inspection_date')
            ->orderByDesc('created_at');
    }

    public function maintenances(): MorphMany
    {
        return $this->morphMany(Maintenance::class, 'maintainable')
            ->with('inspection')
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

    public function scopeFilterName($query, ?string $name)
    {
        if ($name) {
            $query->where('name', 'like', "%{$name}%");
        }

        return $query;
    }

    public function scopeActive($query, $isActive)
    {
        if (!is_null($isActive) && $isActive !== '') {
            $query->where('is_active', $isActive == '1');
        }

        return $query;
    }

    public function scopeAvailable($query, $available)
    {
        if (!is_null($available) && $available !== '') {
            $available == '1'
                ? $query->where('quantity_available', '>', 0)
                : $query->where('quantity_available', '<=', 0);
        }

        return $query;
    }

    public function scopeDigital($query, $isDigital)
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
            'name' => 'Nome do Material',
            'is_digital' => 'Material Digital',
            'notes' => 'Descrição',
            'asset_code' => 'Código de Patrimônio',
            'quantity' => 'Quantidade Total',
            'conservation_state' => 'Estado de Conservação',
            'status_id' => 'Status do Recurso',
            'is_active' => 'Cadastro Ativo',
            'deficiencies' => 'Público-Alvo',
            'trainings' => 'Treinamentos',
            'accessibility_features' => 'Recursos de Acessibilidade',
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

        if ($field === 'accessibility_features' && is_array($value)) {
            return AccessibilityFeature::whereIn('id', $value)
                ->pluck('name')
                ->join(', ') ?: 'Nenhum';
        }

        if ($field === 'is_digital') {
            return $value ? 'Digital' : 'Físico';
        }

        if ($field === 'status_id') {
            return ResourceStatus::find($value)?->name ?? "ID: $value";
        }

        return null;
    }
}
