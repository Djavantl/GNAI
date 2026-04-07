<?php

namespace App\Models\InclusiveRadar;

use App\Models\Traits\Reportable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class InstitutionalEvent extends Model
{
    use HasFactory, Reportable;

    protected $table = 'institutional_events';

    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'location',
        'organizer',
        'audience',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'is_active' => 'boolean',
    ];

    public static function getReportLabel(): string
    {
        return 'Eventos Institucionais';
    }

    public static function getReportColumns(): array
    {
        return [
            'id',
            'title',
            'description',
            'organizer',
            'audience',
            'location',
            'start_date',
            'end_date',
            'is_active',
            'created_at',
        ];
    }

    public static function getReportColumnLabels(): array
    {
        return [
            'id'          => 'ID',
            'title'       => 'Título',
            'description' => 'Descrição',
            'organizer'   => 'Organizador',
            'audience'    => 'Público-Alvo',
            'location'    => 'Local',
            'start_date'  => 'Data de Início',
            'end_date'    => 'Data de Término',
            'is_active'   => 'Ativo',
            'created_at'  => 'Data de Cadastro',
        ];
    }

    /**
     * Scope para buscar por título
     */
    public function scopeSearchTitle(Builder $query, ?string $title): Builder
    {
        if ($title) {
            return $query->where('title', 'like', "%{$title}%");
        }
        return $query;
    }

    /**
     * Scope para eventos ativos
     */
    public function scopeActive(Builder $query, bool $active = true): Builder
    {
        return $query->where('is_active', $active);
    }
}
