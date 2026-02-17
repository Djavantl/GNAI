<?php

namespace App\Models\InclusiveRadar;

use App\Models\AuditLog;
use App\Models\SpecializedEducationalSupport\Deficiency;
use App\Enums\InclusiveRadar\ConservationState;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\Auditable;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class AssistiveTechnology extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $table = 'assistive_technologies';

    protected $fillable = [
        'name',
        'description',
        'type_id',
        'asset_code',
        'quantity',
        'quantity_available',
        'requires_training',
        'conservation_state',
        'notes',
        'status_id',
        'is_active',
    ];

    protected $casts = [
        'requires_training' => 'boolean',
        'is_active' => 'boolean',
        'conservation_state' => ConservationState::class,
    ];

    // Relacionamentos

    public function type(): BelongsTo
    {
        return $this->belongsTo(ResourceType::class, 'type_id');
    }

    public function resourceStatus(): BelongsTo
    {
        return $this->belongsTo(ResourceStatus::class, 'status_id');
    }

    public function deficiencies(): BelongsToMany
    {
        return $this->belongsToMany(
            Deficiency::class,
            'assistive_technology_deficiency',
            'assistive_technology_id',
            'deficiency_id'
        );
    }

    public function attributeValues(): MorphMany
    {
        return $this->morphMany(ResourceAttributeValue::class, 'resource');
    }

    public function inspections(): MorphMany
    {
        return $this->morphMany(Inspection::class, 'inspectable')
            ->with('images')
            ->orderByDesc('inspection_date')
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

    public function loans(): MorphMany
    {
        return $this->morphMany(Loan::class, 'loanable');
    }

    public function logs(): MorphMany
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }

    // Scopes

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

    public function scopeByType(Builder $query, $type): Builder
    {
        if (!is_null($type) && $type !== '') {
            $query->whereHas('type', fn($q) =>
            $q->where('name', 'like', "%{$type}%")
            );
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
            $query->whereHas('type', fn($q) =>
            $q->where('is_digital', $isDigital == '1')
            );
        }

        return $query;
    }

    public static function getAuditLabels(): array
    {
        return [
            'name' => 'Nome da Tecnologia',
            'description' => 'Descrição',
            'asset_code' => 'Código de Patrimônio',
            'quantity' => 'Quantidade Total',
            'requires_training' => 'Requer Treinamento',
            'conservation_state' => 'Estado de Conservação',
            'status_id' => 'Status do Recurso',
            'type_id' => 'Tipo de Recurso',
            'is_active' => 'Cadastro Ativo',
            'deficiencies' => 'Público-Alvo (Deficiências)',
            'attributes' => 'Características Técnicas (Atributos)',
        ];
    }

    public static function formatAuditValue(string $field, $value): ?string
    {
        if ($field === 'deficiencies' && is_array($value)) {
            return Deficiency::whereIn('id', $value)
                ->pluck('name')
                ->join(', ') ?: 'Nenhuma';
        }

        if ($field === 'attributes' && is_array($value)) {
            $lines = [];
            foreach ($value as $attrId => $val) {
                $attrModel = TypeAttribute::find($attrId);
                $label = $attrModel ? $attrModel->label : "Atributo #$attrId";
                $prettyVal = ($val === "1" || $val === true) ? 'Sim' : (($val === "0" || $val === false) ? 'Não' : $val);
                $lines[] = "<strong>{$label}:</strong> {$prettyVal}";
            }
            return implode('<br>', $lines);
        }

        if ($field === 'requires_training') {
            return $value ? 'Sim' : 'Não';
        }

        if ($field === 'type_id') {
            return ResourceType::find($value)?->name ?? "ID: $value";
        }

        if ($field === 'status_id') {
            return ResourceStatus::find($value)?->name ?? "ID: $value";
        }

        return null;
    }
}
