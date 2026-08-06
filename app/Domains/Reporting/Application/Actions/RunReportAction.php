<?php

declare(strict_types=1);

namespace App\Domains\Reporting\Application\Actions;

use App\Domains\Reporting\Application\Contracts\ReportSource;
use App\Domains\Reporting\Application\Data\RunReportData;
use App\Domains\Reporting\Application\Services\ReportCatalog;
use App\Domains\Reporting\Application\Services\ReportRelationCatalog;
use App\Domains\Reporting\Domain\DTOs\ReportColumnDTO;
use App\Domains\Reporting\Domain\DTOs\ReportFilterDefinitionDTO;
use App\Domains\Reporting\Domain\DTOs\ReportFilterDTO;
use App\Domains\Reporting\Domain\DTOs\ReportRequestDTO;
use App\Domains\Reporting\Domain\DTOs\ReportResultDTO;
use App\Domains\Reporting\Domain\Enums\ReportOperator;
use App\Domains\Reporting\Domain\Exceptions\ReportingException;
use App\Shared\Infrastructure\Security\RichTextSanitizer;
use BackedEnum;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use JsonException;
use Stringable;

final readonly class RunReportAction
{
    public function __construct(
        private ReportCatalog $catalog,
        private ReportRelationCatalog $relations,
    ) {}

    /**
     * @throws ReportingException
     */
    public function execute(RunReportData $data, ?int $limit = null): ReportResultDTO
    {
        $filterDTOs = array_map(
            static fn (array $filter): ReportFilterDTO => new ReportFilterDTO(
                field: $filter['field'],
                operator: ReportOperator::from($filter['operator']),
                value: $filter['value'],
            ),
            $data->filters,
        );

        $reportDTO = new ReportRequestDTO(
            source: $data->source,
            columns: array_values($data->columns),
            filters: $filterDTOs,
            limit: $limit ?? $data->limit,
            filterRelatedValues: $data->filterRelatedValues,
        );

        $source = $this->catalog->get($reportDTO->source);
        $columns = collect($source->columns())->keyBy(
            fn (ReportColumnDTO $column): string => $column->key,
        );
        $filterDefinitions = collect($source->filters())->keyBy(
            fn (ReportFilterDefinitionDTO $filter): string => $filter->key,
        );
        $relations = $this->relations->for($source);

        foreach ($relations as $relationName => $relation) {
            foreach ($relation['column_dtos'] as $column) {
                $columns->put(
                    "{$relationName}.{$column->key}",
                    new ReportColumnDTO(
                        key: "{$relationName}.{$column->key}",
                        label: "{$relation['label']} › {$column->label}",
                        type: $column->type,
                        options: $column->options,
                    ),
                );
            }

            foreach ($relation['filters'] as $filter) {
                $filterDefinitions->put(
                    "{$relationName}.{$filter->key}",
                    new ReportFilterDefinitionDTO(
                        key: "{$relationName}.{$filter->key}",
                        label: "{$relation['label']} › {$filter->label}",
                        type: $filter->type,
                        operators: $filter->operators,
                        options: $filter->options,
                    ),
                );
            }

            foreach ($relation['pivot']['columns'] ?? [] as $pivotColumn => $pivotLabel) {
                $key = "{$relationName}.pivot.{$pivotColumn}";
                $columns->put($key, new ReportColumnDTO($key, "{$relation['label']} (vínculo) › {$pivotLabel}"));
            }
        }

        foreach ($reportDTO->columns as $column) {
            if (! $columns->has($column)) {
                throw new ReportingException("A coluna {$column} não está disponível neste relatório.");
            }
        }

        $query = $source->query($reportDTO);

        $selectedRelations = collect([...$reportDTO->columns, ...array_map(
            static fn (ReportFilterDTO $filter): string => $filter->field,
            $reportDTO->filters,
        )])
            ->filter(static fn (string $field): bool => str_contains($field, '.'))
            ->map(static fn (string $field): string => explode('.', $field, 2)[0])
            ->filter(static fn (string $relation): bool => isset($relations[$relation]))
            ->unique()
            ->values()
            ->all();

        if ($selectedRelations !== []) {
            $eagerLoads = collect($selectedRelations)->flatMap(function (string $relationName) use ($relations): array {
                $eloquentRelation = $relations[$relationName]['eloquent_relation'] ?? $relationName;

                if (isset($relations[$relationName]['morph_type'])) {
                    return [$eloquentRelation];
                }

                return [
                    $eloquentRelation,
                    ...array_map(
                        static fn (string $nested): string => "{$eloquentRelation}.{$nested}",
                        $relations[$relationName]['source']->eagerLoads(),
                    ),
                ];
            })->unique()->values()->all();

            $query->with($eagerLoads);
        }

        foreach ($reportDTO->filters as $filter) {
            /** @var ReportFilterDefinitionDTO|null $definition */
            $definition = $filterDefinitions->get($filter->field);

            if ($definition === null) {
                throw new ReportingException("O filtro {$filter->field} não está disponível neste relatório.");
            }

            if (! in_array($filter->operator, $definition->operators, true)) {
                throw new ReportingException('O operador selecionado não é permitido para esse filtro.');
            }

            if ($definition->options !== []
                && ! array_key_exists((string) $filter->value, $definition->options)) {
                throw new ReportingException('O valor selecionado não é permitido para esse filtro.');
            }

            if (! str_contains($filter->field, '.')) {
                $source->applyFilter($query, $filter->field, $filter->operator->sqlOperator(), $filter->value);

                continue;
            }

            [$relationName, $relatedField] = explode('.', $filter->field, 2);
            $relation = $relations[$relationName];
            if (str_starts_with($relatedField, 'pivot.')) {
                $pivotColumn = substr($relatedField, 6);
                $pivotTable = $relation['pivot']['table'] ?? null;

                if ($pivotTable === null) {
                    throw new ReportingException('O filtro de vínculo não está disponível nesta relação.');
                }

                $query->whereHas($relationName, function ($relatedQuery) use ($pivotTable, $pivotColumn, $filter): void {
                    $operator = $filter->operator->sqlOperator();
                    $value = $operator === 'like' ? '%'.trim((string) $filter->value).'%' : $filter->value;
                    $relatedQuery->where("{$pivotTable}.{$pivotColumn}", $operator, $value);
                });

                continue;
            }

            if (isset($relation['morph_type'])) {
                $query->whereHasMorph(
                    $relation['eloquent_relation'],
                    [$relation['morph_class']],
                    function ($relatedQuery) use ($relation, $relatedField, $filter): void {
                        $relation['source']->applyFilter(
                            $relatedQuery,
                            $relatedField,
                            $filter->operator->sqlOperator(),
                            $filter->value,
                        );
                    },
                );

                continue;
            }

            $query->whereHas($relationName, function ($relatedQuery) use ($relation, $relatedField, $filter): void {
                $relation['source']->applyFilter(
                    $relatedQuery,
                    $relatedField,
                    $filter->operator->sqlOperator(),
                    $filter->value,
                );
            });
        }

        $rows = $query
            ->limit($reportDTO->limit)
            ->get()
            ->map(function (Model $model) use ($source, $relations, $reportDTO, $columns): array {
                $row = [];

                foreach ($reportDTO->columns as $column) {
                    $row[str_replace('.', '__', $column)] = $this->format(
                        $this->value($source, $relations, $model, $column, $reportDTO),
                        $columns->get($column),
                    );
                }

                return $row;
            })
            ->values()
            ->all();

        $headers = [];
        foreach ($reportDTO->columns as $column) {
            $headers[$column] = $columns->get($column)->label;
        }

        return new ReportResultDTO(rows: $rows, headers: $headers);
    }

    /** @param array<string, array<string, mixed>> $relations */
    private function value(
        ReportSource $source,
        array $relations,
        Model $model,
        string $column,
        ReportRequestDTO $request,
    ): mixed {
        if (! str_contains($column, '.')) {
            return $source->value($model, $column);
        }

        [$relationName, $relatedColumn] = explode('.', $column, 2);
        $relation = $relations[$relationName];
        $related = $this->relatedValue($model, $relation, $relationName);

        if (isset($relation['morph_class'])) {
            $morphClass = $relation['morph_class'];

            if (! $related instanceof $morphClass) {
                return null;
            }
        }

        if ($related instanceof Collection) {
            $related = $this->filteredItems($related, $relationName, $relation, $request);
        }

        if (str_starts_with($relatedColumn, 'pivot.')) {
            $pivotColumn = substr($relatedColumn, 6);

            if ($related instanceof Model) {
                return data_get($related, "pivot.{$pivotColumn}");
            }

            return $related instanceof Collection
                ? $this->collectionValue($related, static fn (Model $item): mixed => data_get($item, "pivot.{$pivotColumn}"))
                : null;
        }

        if ($related instanceof Collection) {
            return $this->collectionValue(
                $related,
                static fn (Model $item): mixed => $relation['source']->value($item, $relatedColumn),
            );
        }

        return $related instanceof Model
            ? $relation['source']->value($related, $relatedColumn)
            : null;
    }

    /** @param array<string, mixed> $relation */
    private function relatedValue(Model $model, array $relation, string $relationName): mixed
    {
        return $model->getRelation($relation['eloquent_relation'] ?? $relationName);
    }

    /** @param array<string, mixed> $relation */
    private function filteredItems(
        Collection $items,
        string $relationName,
        array $relation,
        ReportRequestDTO $request,
    ): Collection {
        if (! $request->filterRelatedValues) {
            return $items;
        }

        $filters = collect($request->filters)->filter(
            static fn (ReportFilterDTO $filter): bool => str_starts_with($filter->field, $relationName.'.'),
        );

        if ($filters->isEmpty()) {
            return $items;
        }

        return $items->filter(function (Model $item) use ($filters, $relation, $relationName): bool {
            return $filters->every(function (ReportFilterDTO $filter) use ($item, $relation, $relationName): bool {
                $field = substr($filter->field, strlen($relationName) + 1);
                $actual = str_starts_with($field, 'pivot.')
                    ? data_get($item, $field)
                    : $relation['source']->value($item, $field);

                if ($actual instanceof BackedEnum) {
                    $actual = $actual->value;
                } elseif ($actual instanceof CarbonInterface) {
                    $actual = $actual->toDateString();
                }

                return match ($filter->operator) {
                    ReportOperator::EQUALS => (string) $actual === (string) $filter->value,
                    ReportOperator::NOT_EQUALS => (string) $actual !== (string) $filter->value,
                    ReportOperator::CONTAINS => mb_stripos((string) $actual, (string) $filter->value) !== false,
                    ReportOperator::GREATER_THAN => $actual > $filter->value,
                    ReportOperator::GREATER_THAN_OR_EQUALS => $actual >= $filter->value,
                    ReportOperator::LESS_THAN => $actual < $filter->value,
                    ReportOperator::LESS_THAN_OR_EQUALS => $actual <= $filter->value,
                };
            });
        })->values();
    }

    private function collectionValue(Collection $items, callable $resolve): ?string
    {
        $values = $items->map($resolve)
            ->map(fn (mixed $value): string|int|float|null => $this->format($value))
            ->filter(static fn (mixed $value): bool => $value !== null && $value !== '')
            ->unique()
            ->values();

        return $values->isEmpty() ? null : $values->implode(', ');
    }

    /**
     * @throws JsonException
     */
    private function format(mixed $value, ?ReportColumnDTO $column = null): string|int|float|null
    {
        $optionKey = $value instanceof BackedEnum
            ? (string) $value->value
            : (is_scalar($value) ? (string) $value : null);

        if ($column !== null
            && $column->options !== []
            && $optionKey !== null
            && array_key_exists($optionKey, $column->options)) {
            return $column->options[$optionKey];
        }

        if (is_string($value)) {
            return RichTextSanitizer::toPlainText($value);
        }

        if ($value === null || is_int($value) || is_float($value)) {
            return $value;
        }

        if (is_bool($value)) {
            return $value ? 'Sim' : 'Não';
        }

        if ($value instanceof BackedEnum) {
            return method_exists($value, 'label') ? $value->label() : (string) $value->value;
        }

        if ($value instanceof CarbonInterface) {
            return $value->format('d/m/Y');
        }

        if ($value instanceof Stringable) {
            return RichTextSanitizer::toPlainText((string) $value);
        }

        return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    }
}
