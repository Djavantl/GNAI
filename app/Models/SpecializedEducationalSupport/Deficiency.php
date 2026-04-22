<?php

namespace App\Models\SpecializedEducationalSupport;

use App\Models\InclusiveRadar\AccessibleEducationalMaterial;
use App\Models\InclusiveRadar\AssistiveTechnology;
use App\Models\InclusiveRadar\Barrier;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Reportable;
use DomainException;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Deficiency extends Model
{
    
    use hasFactory;
    use Reportable;

    protected $fillable = [
        'name',
        'cid_code',
        'description',
        'is_active',
    ];


    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function students()
    {
        return $this->belongsToMany(Student::class, 'students_deficiencies')
            ->using(StudentDeficiencies::class)
            ->withPivot([
                'id',
                'severity',
                'uses_support_resources',
                'notes'
            ])
            ->withTimestamps();
    }

    public function assistiveTechnologies(): BelongsToMany
    {
        return $this->belongsToMany(
            AssistiveTechnology::class,
            'assistive_technology_deficiency',
            'deficiency_id',
            'assistive_technology_id'
        );
    }

    public function accessibleEducationalMaterials(): BelongsToMany
    {
        return $this->belongsToMany(
            AccessibleEducationalMaterial::class,
            'accessible_educational_material_deficiency',
            'deficiency_id',
            'accessible_educational_material_id'
        );
    }

    public function barriers(): BelongsToMany
    {
        return $this->belongsToMany(
            Barrier::class,
            'barrier_deficiency',
            'deficiency_id',
            'barrier_id'
        )->withTimestamps();
    }

    // Scopes para Filtros
    public function scopeName($query, ?string $term)
    {
        if (!$term) return $query;
        return $query->where('name', 'like', "{$term}%");
    }

    public function scopeCid($query, ?string $term)
    {
        if (!$term) return $query;
        return $query->where('cid_code', 'like', "{$term}%");
    }

    public function scopeActive($query, $isActive)
    {
        if ($isActive === null || $isActive === '') return $query;
        return $query->where('is_active', (bool) $isActive);
    }

     /*
    |--------------------------------------------------------------------------
    | Configuração do Report Builder
    |--------------------------------------------------------------------------
    */

    public static function getReportColumns(): ?array
    {
        return [
            'name',
            'cid_code',
            'description',
            'is_active',
        ];
    }

    public static function getReportColumnLabels(): array
    {
        return [
            'name' => 'Nome da Deficiência',
            'cid_code' => 'Código CID',
            'description'=> 'Descrição',
            'is_active'=> 'Ativa',
        ];
    }

    public static function getReportLabel()
    {
        return 'Deficiência';
    }

    public function ensureCanBeDeactivated(): void
    {
        if ($this->students()->exists()) {

            throw new DomainException(
                "Esta deficiência está vinculada a um ou mais alunos e não pode ser desativada."
            );

        }
    }

    public function ensureIsActive(): void
    {
        if (! $this->is_active) {

            throw new DomainException(
                "Esta deficiência está desativada e não pode ser vinculada a um aluno."
            );

        }
    }
}
