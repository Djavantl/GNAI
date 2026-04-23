<?php

namespace App\Models\InclusiveRadar;

use App\Models\SpecializedEducationalSupport\Student;
use App\Models\SpecializedEducationalSupport\Professional;
use App\Models\Traits\Reportable;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * RF: fila de espera para recursos indisponíveis do radar inclusivo.
 * Uso: solicitação de item, cancelamento, conversão em empréstimo e relatórios.
 */
class Waitlist extends Model
{
    use HasFactory, Reportable;

    /**
     * Identidade e Estado:
     * Mantém os dados da solicitação e os casts usados no fluxo de espera.
     */

    protected $fillable = [
        'waitlistable_id',
        'waitlistable_type',
        'student_id',
        'professional_id',
        'user_id',
        'requested_at',
        'status',
        'observation',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];

    /**
     * Relatórios:
     * Expõe campos e itens polimórficos no builder de relatórios.
     */

    public static function getReportLabel(): string
    {
        return 'Lista de Espera';
    }

    public static function getReportColumns(): array
    {
        return [
            'id',
            'status',
            'observation',
            'requested_at',
            'created_at',
        ];
    }

    public static function getReportColumnLabels(): array
    {
        return [
            'id'           => 'ID',
            'status'       => 'Status',
            'observation'  => 'Observação',
            'requested_at' => 'Data da Solicitação',
            'created_at'   => 'Data de Cadastro',
        ];
    }

    public static function getReportMorphTargets(): array
    {
        return [
            'waitlistable' => [
                AssistiveTechnology::class,
                AccessibleEducationalMaterial::class,
            ],
        ];
    }

    /**
     * Relacionamentos:
     * Conecta a solicitação ao item pedido e aos beneficiários envolvidos.
     */

    public function waitlistable(): MorphTo
    {
        return $this->morphTo();
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function professional(): BelongsTo
    {
        return $this->belongsTo(Professional::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scopes de Operação e Relatório:
     * Reúne filtros usados no index operacional e no report builder.
     */

    public function scopeItem($query, $name = null)
    {
        if (!$name) return $query;

        $name = strtolower($name);

        return $query->whereHasMorph(
            'waitlistable',
            [AssistiveTechnology::class, AccessibleEducationalMaterial::class],
            function ($q) use ($name) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$name}%"]);
            }
        );
    }

    public function scopeStudent($query, $name = null)
    {
        if (!$name) return $query;

        return $query->whereHas('student.person', function ($q) use ($name) {
            $q->where('name', 'like', "%$name%");
        });
    }

    public function scopeProfessional($query, $name = null)
    {
        if (!$name) return $query;

        return $query->whereHas('professional.person', function ($q) use ($name) {
            $q->where('name', 'like', "%$name%");
        });
    }
}
