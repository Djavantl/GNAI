<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Domain\Models;

use App\Domains\InclusiveRadar\Domain\Models\AccessibleEducationalMaterial;
use App\Domains\InclusiveRadar\Domain\Models\AssistiveTechnology;
use App\Domains\InclusiveRadar\Domain\Models\Barrier;
use App\Domains\SpecializedEducationalSupport\Domain\DTOs\Deficiencies\CreateDeficiencyDTO;
use App\Domains\SpecializedEducationalSupport\Domain\DTOs\Deficiencies\UpdateDeficiencyDTO;
use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidDeficiency;
use Database\Factories\Domains\SpecializedEducationalSupport\DeficiencyFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[UseFactory(DeficiencyFactory::class)]
final class Deficiency extends Model
{
    use HasFactory;

    protected $table = 'deficiencies';

    protected $fillable = [
        'name',
        'cid_code',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function register(CreateDeficiencyDTO $data): self
    {
        return new self([
            'name' => self::normalizeName($data->name),
            'cid_code' => self::normalizeCidCode($data->cidCode),
            'description' => self::normalizeDescription($data->description),
            'is_active' => $data->isActive,
        ]);
    }

    public function revise(UpdateDeficiencyDTO $data): void
    {
        $this->fill([
            'name' => self::normalizeName($data->name),
            'cid_code' => self::normalizeCidCode($data->cidCode),
            'description' => self::normalizeDescription($data->description),
        ]);
    }

    /**
     * @throws InvalidDeficiency
     */
    public function ensureCanBeDeactivated(bool $hasStudents): void
    {
        if ($hasStudents) {
            throw new InvalidDeficiency(
                'Este perfil está vinculado a um ou mais alunos e não pode ser desativado.'
            );
        }
    }

    /**
     * @throws InvalidDeficiency
     */
    public function ensureIsActive(): void
    {
        if (! $this->is_active) {
            throw new InvalidDeficiency(
                'Este perfil está desativado e não pode ser vinculado a um aluno.'
            );
        }
    }

    public function activate(): void
    {
        $this->is_active = true;
    }

    public function deactivate(): void
    {
        $this->is_active = false;
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'students_deficiencies')
            ->using(StudentDeficiency::class)
            ->withPivot([
                'id',
                'severity',
                'notes',
            ])
            ->withTimestamps();
    }

    public function assistiveTechnologies(): BelongsToMany
    {
        return $this->belongsToMany(
            AssistiveTechnology::class,
            'assistive_technology_deficiency',
            'deficiency_id',
            'assistive_technology_id',
        )->withTimestamps();
    }

    public function accessibleEducationalMaterials(): BelongsToMany
    {
        return $this->belongsToMany(
            AccessibleEducationalMaterial::class,
            'accessible_educational_material_deficiency',
            'deficiency_id',
            'accessible_educational_material_id',
        )->withTimestamps();
    }

    public function barriers(): BelongsToMany
    {
        return $this->belongsToMany(
            Barrier::class,
            'barrier_deficiency',
            'deficiency_id',
            'barrier_id',
        )->withTimestamps();
    }

    private static function normalizeName(string $name): string
    {
        return trim($name);
    }

    private static function normalizeCidCode(?string $cidCode): ?string
    {
        $cidCode = strtoupper(trim((string) $cidCode));

        return $cidCode === '' ? null : $cidCode;
    }

    private static function normalizeDescription(?string $description): ?string
    {
        $description = trim((string) $description);

        return $description === '' ? null : $description;
    }
}
