<?php

namespace App\Models\InclusiveRadar;

use App\Models\Traits\Reportable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BarrierCategory extends Model
{
    use HasFactory, SoftDeletes, Reportable;

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

    public function barriers(): HasMany
    {
        return $this->hasMany(Barrier::class);
    }

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
