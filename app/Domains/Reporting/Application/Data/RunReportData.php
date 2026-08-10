<?php

declare(strict_types=1);

namespace App\Domains\Reporting\Application\Data;

use App\Domains\Reporting\Domain\Enums\ReportOperator;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
final class RunReportData extends Data
{
    /**
     * @param  list<string>  $columns
     * @param  list<array{field: string, operator: string, value: string|int|float|bool}>  $filters
     */
    public function __construct(
        public string $source,
        public array $columns,
        public array $filters = [],
        public int $limit = 200,
        public bool $filterRelatedValues = false,
    ) {}

    public static function rules(): array
    {
        return [
            'source' => ['required', 'string', 'regex:/^[a-z0-9.-]+$/', 'max:100'],
            'columns' => ['required', 'array', 'min:1', 'max:8'],
            'columns.*' => ['required', 'string', 'distinct', 'max:100'],
            'filters' => ['sometimes', 'array', 'max:12'],
            'filters.*.field' => ['required', 'string', 'max:100'],
            'filters.*.operator' => ['required', 'string', Rule::enum(ReportOperator::class)],
            'filters.*.value' => ['required'],
            'limit' => ['sometimes', 'integer', 'min:1', 'max:1000'],
            'filter_related_values' => ['sometimes', 'boolean'],
        ];
    }

    public static function messages(): array
    {
        return [
            'source.required' => 'Selecione uma fonte de relatório.',
            'source.regex' => 'A fonte de relatório é inválida.',
            'columns.required' => 'Selecione ao menos uma coluna.',
            'columns.max' => 'Selecione no máximo 8 colunas.',
            'columns.*.distinct' => 'Não repita colunas no relatório.',
            'filters.max' => 'Use no máximo 12 filtros.',
            'limit.max' => 'A prévia está limitada a 1.000 registros.',
        ];
    }
}
