<?php

namespace App\Models\InclusiveRadar;

use App\Enums\InclusiveRadar\ConservationState;
use App\Models\AuditLog;
use App\Models\SpecializedEducationalSupport\Deficiency;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Auditable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccessibleEducationalMaterial extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $table = 'accessible_educational_materials';

    protected $fillable = [
        'name',
        'type_id',
        'asset_code',
        'quantity',
        'quantity_available',
        'conservation_state',
        'notes',
        'status_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'conservation_state' => ConservationState::class,
    ];

    // Relacionamentos

    public function trainings(): MorphMany
    {
        return $this->morphMany(Training::class, 'trainable');
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

    public function type(): BelongsTo
    {
        return $this->belongsTo(ResourceType::class, 'type_id');
    }

    public function resourceStatus(): BelongsTo
    {
        return $this->belongsTo(ResourceStatus::class, 'status_id');
    }

    public function attributeValues(): MorphMany
    {
        return $this->morphMany(ResourceAttributeValue::class, 'resource');
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
    
    public function maintenances(): MorphMany
    {
        return $this->morphMany(Maintenance::class, 'maintainable')
            ->with('inspection')
            ->orderByDesc('created_at');
    }

    public function allImages(): HasManyThrough
    {
        return $this->hasManyThrough(
            InspectionImage::class,
            Inspection::class,
            'inspectable_id',
            'inspection_id',
            'id',
            'id'
        )->where('inspectable_type', static::class);
    }

    // QUERY SCOPES

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

    public function scopeByType($query, $type)
    {
        if (!is_null($type) && $type !== '') {
            $query->whereHas('type', function ($q) use ($type) {
                $q->where('name', 'like', "%{$type}%");
            });
        }

        return $query;
    }

    public function scopeAvailable($query, $available)
    {
        if (!is_null($available) && $available !== '') {
            if ($available == '1') {
                $query->where('quantity_available', '>', 0);
            } else {
                $query->where('quantity_available', '<=', 0);
            }
        }

        return $query;
    }

    public function scopeDigital($query, $isDigital)
    {
        if (!is_null($isDigital) && $isDigital !== '') {
            $query->whereHas('type', function ($q) use ($isDigital) {
                $q->where('is_digital', $isDigital == '1');
            });
        }

        return $query;
    }

    // Auditoria

    public static function getAuditLabels(): array
    {
        return [
            'name' => 'Nome do Material',
            'asset_code' => 'Código de Patrimônio',
            'quantity' => 'Quantidade',
            'conservation_state' => 'Estado de Conservação',
            'status_id' => 'Status',
            'type_id' => 'Tipo',
            'is_active' => 'Cadastro Ativo',
            'deficiencies' => 'Público-Alvo',
            'trainings' => 'Treinamentos',
            'attributes' => 'Características Técnicas',
            'accessibility_features' => 'Recursos de Acessibilidade',
        ];
    }

    public static function formatAuditValue(string $field, $value): ?string
    {
        // --- Características Técnicas (Atributos Dinâmicos) ---
        if ($field === 'attributes' && is_array($value)) {
            $attributeNames = \App\Models\InclusiveRadar\TypeAttribute::whereIn('id', array_keys($value))
                ->pluck('name', 'id')
                ->toArray();

            $formatted = [];
            foreach ($value as $id => $val) {
                // Buscamos o nome e aplicamos o ucfirst para garantir a primeira letra maiúscula
                $name = $attributeNames[$id] ?? "Atributo #$id";
                $label = mb_convert_case($name, MB_CASE_TITLE, "UTF-8");

                $formatted[] = "{$label}: {$val}";
            }

            return !empty($formatted) ? implode(' | ', $formatted) : 'Nenhuma';
        }

        // --- Público-Alvo ---
        if ($field === 'deficiencies' && is_array($value)) {
            return Deficiency::whereIn('id', $value)
                ->pluck('name')
                ->join(', ') ?: 'Nenhuma';
        }

        // --- Treinamentos ---
        if ($field === 'trainings' && is_array($value)) {
            return Training::whereIn('id', $value)
                ->pluck('title')
                ->join(', ') ?: 'Nenhum';
        }

        // --- Recursos de Acessibilidade ---
        if ($field === 'accessibility_features' && is_array($value)) {
            return AccessibilityFeature::whereIn('id', $value)
                ->pluck('name')
                ->join(', ') ?: 'Nenhum';
        }

        // --- Relacionamentos Diretos (ID para Nome) ---
        if ($field === 'type_id') {
            return ResourceType::find($value)?->name ?? "ID: $value";
        }

        if ($field === 'status_id') {
            return ResourceStatus::find($value)?->name ?? "ID: $value";
        }

        return null;
    }

}
