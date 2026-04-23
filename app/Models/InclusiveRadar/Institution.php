<?php

namespace App\Models\InclusiveRadar;

use App\Models\Traits\Reportable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * RF: cadastro das instituições/unidades usadas como base territorial do radar.
 * Uso: mapas, locais, barreiras e relatórios institucionais.
 */
class Institution extends Model
{
    use HasFactory, SoftDeletes, Reportable;

    /**
     * Identidade e Persistência:
     * Reúne os dados cadastrais da instituição e preserva histórico com soft delete.
     */

    protected $table = 'institutions';

    protected $fillable = [
        'name',
        'short_name',
        'city',
        'state',
        'district',
        'address',
        'latitude',
        'longitude',
        'default_zoom',
        'is_active',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'default_zoom' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Relatórios:
     * Define campos e coleções liberadas para o report builder do radar.
     */

    public static function getReportLabel(): string
    {
        return 'Instituições';
    }

    public static function getReportColumns(): array
    {
        return [
            'id',
            'name',
            'short_name',
            'city',
            'state',
            'district',
            'address',
            'is_active',
            'created_at',
        ];
    }

    public static function getReportColumnLabels(): array
    {
        return [
            'id'         => 'ID',
            'name'       => 'Nome',
            'short_name' => 'Nome Abreviado',
            'city'       => 'Cidade',
            'state'      => 'Estado',
            'district'   => 'Bairro',
            'address'    => 'Endereço',
            'is_active'  => 'Ativo',
            'created_at' => 'Data de Cadastro',
        ];
    }

    public static function getReportCollectionRelations(): array
    {
        return ['locations', 'barriers'];
    }

    /**
     * Relacionamentos:
     * Esses vínculos alimentam mapas, dashboards e páginas de detalhe.
     */

    public function latestInspection(): MorphOne
    {
        return $this->morphOne(Inspection::class, 'inspectable')
            ->latestOfMany('inspection_date');
    }

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }

    public function barriers(): HasMany
    {
        return $this->hasMany(Barrier::class);
    }

    /**
     * Scopes de Listagem:
     * Mantém os filtros reutilizados no index e em consultas administrativas.
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

    public function scopeFilterLocation($query, ?string $location)
    {
        if ($location) {
            $query->where(function ($q) use ($location) {
                $q->where('city', 'like', "%{$location}%")
                    ->orWhere('state', 'like', "%{$location}%");
            });
        }
    }
}
