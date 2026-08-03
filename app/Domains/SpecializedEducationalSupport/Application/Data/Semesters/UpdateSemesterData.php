<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Data\Semesters;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[MapInputName(SnakeCaseMapper::class)]
final class UpdateSemesterData extends Data
{
    public function __construct(
        public int $year,
        public int $term,
        public ?string $startDate = null,
        public ?string $endDate = null,
        public bool $isCurrent = false,
    ) {}

    public static function rules(ValidationContext $context): array
    {
        $term = (int) ($context->fullPayload['term'] ?? 0);
        $routeSemester = request()->route('semester');
        $semesterId = $routeSemester instanceof Model
            ? $routeSemester->getKey()
            : $routeSemester;

        return [
            'year' => [
                'required',
                'integer',
                'between:1901,2155',
                Rule::unique('semesters', 'year')
                    ->where(fn (Builder $query): Builder => $query->where('term', $term))
                    ->ignore($semesterId),
            ],
            'term' => ['required', 'integer', Rule::in([1, 2])],
            'start_date' => ['nullable', 'date', 'required_with:end_date'],
            'end_date' => ['nullable', 'date', 'required_with:start_date', 'after_or_equal:start_date'],
            'is_current' => ['sometimes', 'boolean'],
        ];
    }

    public static function messages(): array
    {
        return [
            'year.required' => 'O ano letivo é obrigatório.',
            'year.integer' => 'O ano letivo deve ser um número inteiro.',
            'year.between' => 'O ano letivo deve estar entre 1901 e 2155.',
            'year.unique' => 'Já existe um semestre cadastrado para este ano e período.',
            'term.required' => 'O período letivo é obrigatório.',
            'term.integer' => 'O período letivo deve ser um número inteiro.',
            'term.in' => 'O período letivo deve ser o primeiro ou o segundo semestre.',
            'start_date.required_with' => 'Informe a data de início quando a data de término for preenchida.',
            'start_date.date' => 'A data de início deve ser uma data válida.',
            'end_date.required_with' => 'Informe a data de término quando a data de início for preenchida.',
            'end_date.date' => 'A data de término deve ser uma data válida.',
            'end_date.after_or_equal' => 'A data de término não pode ser anterior à data de início.',
            'is_current.boolean' => 'A indicação de semestre atual é inválida.',
        ];
    }
}
