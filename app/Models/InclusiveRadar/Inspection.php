<?php

namespace App\Models\InclusiveRadar;

use App\Enums\InclusiveRadar\BarrierStatus;
use App\Enums\InclusiveRadar\ConservationState;
use App\Enums\InclusiveRadar\InspectionType;
use App\Models\Traits\Reportable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * RF: histórico de vistorias dos itens e barreiras do radar inclusivo.
 * Uso: timelines de show, filtros por status/estado e report builder.
 */
class Inspection extends Model
{
    use HasFactory, Reportable;

    /*
    |--------------------------------------------------------------------------
    | Identidade e Estado
    |--------------------------------------------------------------------------
    | Mantém os campos de vistoria usados por recursos e barreiras.
    */

    protected $fillable = [
        'inspectable_id',
        'inspectable_type',
        'state',
        'status',
        'inspection_date',
        'description',
        'type',
        'user_id'
    ];

    protected $casts = [
        'inspection_date' => 'date',
        'state' => ConservationState::class,
        'status' => BarrierStatus::class,
        'type' => InspectionType::class,
    ];

    /*
    |--------------------------------------------------------------------------
    | Helpers e Relatórios
    |--------------------------------------------------------------------------
    | Dá suporte à exibição legível do item inspecionado na UI e no builder.
    */

    public function getInspectableNameAttribute(): ?string
    {
        return $this->inspectable?->name ?? null;
    }

    public static function getReportLabel(): string
    {
        return 'Inspeções';
    }

    public static function getReportColumns(): array
    {
        return [
            'id',
            'state',
            'status',
            'inspectable_name',
            'type',
            'inspection_date',
            'description',
            'created_at',
        ];
    }

    public static function getReportColumnLabels(): array
    {
        return [
            'id'              => 'ID',
            'state'           => 'Estado de Conservação',
            'status'          => 'Status da Barreira',
            'inspectable_name' => 'Item Inspecionado',
            'type'            => 'Tipo de Inspeção',
            'inspection_date' => 'Data da Inspeção',
            'description'     => 'Descrição',
            'created_at'      => 'Data de Cadastro',
        ];
    }

    public static function getReportMorphTargets(): array
    {
        return [
            'inspectable' => [
                AssistiveTechnology::class,
                AccessibleEducationalMaterial::class,
                Barrier::class,
            ],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relacionamentos
    |--------------------------------------------------------------------------
    | O vínculo polimórfico conecta a vistoria ao item real do domínio.
    */

    public function inspectable(): MorphTo
    {
        return $this->morphTo();
    }

    public function images(): HasMany
    {
        return $this->hasMany(InspectionImage::class, 'inspection_id');
    }

}
