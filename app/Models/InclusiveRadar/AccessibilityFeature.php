<?php

namespace App\Models\InclusiveRadar;

use App\Models\Traits\Reportable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AccessibilityFeature extends Model
{
    use HasFactory, Reportable;

    protected $table = 'accessibility_features';

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function getReportLabel(): string
    {
        return 'Recursos de Acessibilidade';
    }

    public static function getReportColumns(): array
    {
        return [
            'id',
            'name',
            'description',
            'is_active',
        ];
    }

    public static function getReportColumnLabels(): array
    {
        return [
            'id'          => 'ID',
            'name'        => 'Nome do Recurso',
            'description' => 'Descrição',
            'is_active'   => 'Ativo',
        ];
    }

    public function materials(): BelongsToMany
    {
        return $this->BelongsToMany(
            AccessibleEducationalMaterial::class,
            'accessible_educational_material_accessibility'
        );
    }

    public function scopeFilterName($query, ?string $name)
    {
        if ($name) {
            $query->where('name', 'like', "%{$name}%");
        }
    }

    public function scopeFilterStatus($query, $status)
    {
        if ($status !== null && $status !== '') {
            $query->where('is_active', $status);
        }
    }
}
