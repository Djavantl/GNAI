<?php

namespace App\Models\InclusiveRadar;

use App\Models\Traits\Reportable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * RF: cadastro dos recursos de acessibilidade vinculáveis aos materiais.
 * Uso: formulários de MPA, telas de show e report builder do módulo.
 */
class AccessibilityFeature extends Model
{
    use HasFactory, Reportable;

    /**
     * Identidade e Persistência:
     * Centraliza os campos básicos do cadastro para uso em CRUD e filtros.
     */

    protected $table = 'accessibility_features';

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relatórios:
     * Define como a entidade aparece no builder de relatórios do sistema.
     */

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

    /**
     * Relacionamentos:
     * Mantém o vínculo N:N com materiais para consultas e telas de detalhe.
     */

    public function materials(): BelongsToMany
    {
        return $this->BelongsToMany(
            AccessibleEducationalMaterial::class,
            'accessible_educational_material_accessibility'
        );
    }

    /**
     * Scopes de Listagem:
     * Sustenta as filtragens do index e reaproveitamento em relatórios/serviços.
     */

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
