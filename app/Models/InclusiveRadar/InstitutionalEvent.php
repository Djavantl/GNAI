<?php

namespace App\Models\InclusiveRadar;

use App\Domains\InclusiveRadar\Domain\Models\InstitutionalEvent as DomainInstitutionalEvent;
use App\Models\Traits\Reportable;
use Database\Factories\InclusiveRadar\InstitutionalEventFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Builder;

/**
 * RF: agenda institucional do radar inclusivo.
 * Uso: calendário/listagens, lembretes automáticos e relatórios de eventos.
 */
/**
 * @deprecated Use App\Domains\InclusiveRadar\Domain\Models\InstitutionalEvent.
 */
#[UseFactory(InstitutionalEventFactory::class)]
class InstitutionalEvent extends DomainInstitutionalEvent
{
    use Reportable;

    /**
     * Relatórios:
     * Define a exposição da agenda institucional no report builder.
     */

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

    public function scopeSearchTitle(Builder $query, ?string $title): Builder
    {
        if ($title) {
            return $query->where('title', 'like', "%{$title}%");
        }
        return $query;
    }

    public function scopeActive(Builder $query, bool $active = true): Builder
    {
        return $query->where('is_active', $active);
    }
}
