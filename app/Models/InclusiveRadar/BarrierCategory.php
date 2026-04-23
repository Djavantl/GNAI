<?php

namespace App\Models\InclusiveRadar;

use App\Models\Traits\Reportable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * RF: classificação funcional das barreiras registradas no mapa inclusivo.
 * Uso: cadastro de barreiras, validação de mapa e relatórios de categorias.
 */
class BarrierCategory extends Model
{
    use HasFactory, SoftDeletes, Reportable;

    /**
     * Identidade e Persistência:
     * Mantém o núcleo do cadastro e o soft delete para histórico administrativo.
     */

    protected $table = 'barrier_categories';

    protected $fillable = [
        'name',
        'description',
        'is_active',
        'blocks_map'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'blocks_map' => 'boolean'
    ];

    /**
     * Relatórios:
     * Expõe colunas e coleções liberadas para o report builder do módulo.
     */

    public static function getReportLabel(): string
    {
        return 'Categorias de Barreira';
    }

    public static function getReportColumns(): array
    {
        return [
            'id',
            'name',
            'description',
            'is_active',
            'blocks_map',
            'created_at',
        ];
    }

    public static function getReportColumnLabels(): array
    {
        return [
            'id'          => 'ID',
            'name'        => 'Nome',
            'description' => 'Descrição',
            'is_active'   => 'Ativo',
            'blocks_map'  => 'Bloqueia Mapa',
            'created_at'  => 'Data de Cadastro',
        ];
    }

    public static function getReportCollectionRelations(): array
    {
        return ['barriers'];
    }

    /**
     * Relacionamentos:
     * A categoria agrega barreiras e influencia o comportamento do mapa.
     */

    public function barriers(): HasMany
    {
        return $this->hasMany(Barrier::class);
    }

    /**
     * Scopes de Listagem:
     * Reúne a filtragem usada nas listagens administrativas e consultas auxiliares.
     */

    public function scopeFilterName($query, ?string $name): Builder
    {
        return $name ? $query->where('name', 'like', "%{$name}%") : $query;
    }

    public function scopeFilterActive($query, $isActive): Builder
    {
        if (!is_null($isActive) && $isActive !== '') {
            $query->where('is_active', $isActive == '1');
        }
        return $query;
    }
}
