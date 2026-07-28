<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Domain\Models;

use App\Domains\SpecializedEducationalSupport\Domain\DTOs\Semesters\CreateSemesterDTO;
use App\Domains\SpecializedEducationalSupport\Domain\DTOs\Semesters\UpdateSemesterDTO;
use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidSemester;
use App\Models\SpecializedEducationalSupport\Pei;
use Carbon\CarbonImmutable;
use Database\Factories\Domains\SpecializedEducationalSupport\SemesterFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[UseFactory(SemesterFactory::class)]
final class Semester extends Model
{
    use HasFactory;

    protected $table = 'semesters';

    protected $fillable = [
        'year',
        'term',
        'label',
        'start_date',
        'end_date',
        'is_current',
    ];

    protected $casts = [
        'year' => 'integer',
        'term' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
    ];

    /**
     * @throws InvalidSemester
     */
    public static function register(CreateSemesterDTO $data): self
    {
        return new self(self::attributesFrom($data));
    }

    /**
     * @throws InvalidSemester
     */
    public function revise(UpdateSemesterDTO $data): void
    {
        $this->fill(self::attributesFrom($data));
    }

    public function markAsCurrent(): void
    {
        $this->is_current = true;
    }

    public function markAsHistorical(): void
    {
        $this->is_current = false;
    }

    /**
     * @throws InvalidSemester
     */
    public function ensureCanBeDeleted(bool $hasLinkedRecords): void
    {
        if ($this->is_current) {
            throw new InvalidSemester(
                'Não é possível excluir o semestre atual do sistema.'
            );
        }

        if ($hasLinkedRecords) {
            throw new InvalidSemester(
                'Este semestre possui registros vinculados e não pode ser excluído.'
            );
        }
    }

    public function studentContexts(): HasMany
    {
        return $this->hasMany(StudentContext::class, 'semester_id');
    }

    public function studentDocuments(): HasMany
    {
        return $this->hasMany(StudentDocument::class, 'semester_id');
    }

    public function peis(): HasMany
    {
        return $this->hasMany(Pei::class, 'semester_id');
    }

    /**
     * @return array<string, int|string|bool|null>
     *
     * @throws InvalidSemester
     */
    private static function attributesFrom(CreateSemesterDTO|UpdateSemesterDTO $data): array
    {
        self::ensureValidPeriod(
            year: $data->year,
            term: $data->term,
            startDate: $data->startDate,
            endDate: $data->endDate,
        );

        return [
            'year' => $data->year,
            'term' => $data->term,
            'label' => "{$data->year}.{$data->term}",
            'start_date' => self::normalizeDate($data->startDate),
            'end_date' => self::normalizeDate($data->endDate),
            'is_current' => $data->isCurrent,
        ];
    }

    /**
     * @throws InvalidSemester
     */
    private static function ensureValidPeriod(int $year, int $term, ?string $startDate, ?string $endDate): void
    {
        if ($year < 1901 || $year > 2155) {
            throw new InvalidSemester('O ano letivo deve estar entre 1901 e 2155.');
        }

        if (! in_array($term, [1, 2], true)) {
            throw new InvalidSemester('O período letivo deve ser o primeiro ou o segundo semestre.');
        }

        if (($startDate === null) !== ($endDate === null)) {
            throw new InvalidSemester('Informe as datas de início e término do semestre.');
        }

        if ($startDate !== null && CarbonImmutable::parse($endDate)->lt(CarbonImmutable::parse($startDate))) {
            throw new InvalidSemester('A data de término não pode ser anterior à data de início.');
        }
    }

    private static function normalizeDate(?string $date): ?string
    {
        return $date === null
            ? null
            : CarbonImmutable::parse($date)->toDateString();
    }
}
