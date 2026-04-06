<?php

namespace App\Models\InclusiveRadar;

use App\Audit\Contracts\Auditable as AuditableContract;
use App\Audit\Formatters\InclusiveRadar\AccessibleEducationalMaterialFormatter;
use App\Enums\InclusiveRadar\ConservationState;
use App\Enums\InclusiveRadar\ResourceStatus;
use App\Models\AuditLog;
use App\Models\SpecializedEducationalSupport\Deficiency;
use App\Models\Traits\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccessibleEducationalMaterial extends Model implements AuditableContract
{
    use HasFactory, SoftDeletes, Auditable;

    protected $table = 'accessible_educational_materials';

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

    protected array $auditExclude = ['quantity_available'];

    public static function auditLabels(): array
    {
        return [
            'name'                  => 'Nome do Material',
            'is_digital'            => 'Material Digital',
            'notes'                 => 'Descrição',
            'asset_code'            => 'Código de Patrimônio',
            'quantity'              => 'Quantidade Total',
            'conservation_state'    => 'Estado de Conservação',
            'status'                => 'Status do Recurso',
            'is_active'             => 'Cadastro Ativo',
            'deficiencies'          => 'Público-Alvo',
            'accessibility_features'=> 'Recursos de Acessibilidade',
        ];
    }

    public static function auditFormatter(): string
    {
        return AccessibleEducationalMaterialFormatter::class;
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
}
