<?php

declare(strict_types=1);

namespace App\Domains\Reporting\Application\Services;

use App\Domains\Reporting\Application\Contracts\ReportSource;
use App\Domains\Reporting\Domain\DTOs\ReportColumnDTO;
use App\Domains\Reporting\Domain\DTOs\ReportFilterDefinitionDTO;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Relation;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use Throwable;

final readonly class ReportRelationCatalog
{
    public function __construct(private ReportCatalog $sources) {}

    /** @return array<string, array<string, mixed>> */
    public function for(ReportSource $source): array
    {
        $modelClass = $source->modelClass();
        $model = new $modelClass;
        $relationLabels = $source->relationLabels();
        $excludedRelations = $source->excludedRelations();
        $relations = [];

        foreach ((new ReflectionClass($modelClass))->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->class !== $modelClass || $method->getNumberOfRequiredParameters() !== 0) {
                continue;
            }

            $returnType = $method->getReturnType();
            if (! $returnType instanceof ReflectionNamedType
                || $returnType->isBuiltin()
                || ! is_a($returnType->getName(), Relation::class, true)) {
                continue;
            }

            try {
                $relation = $method->invoke($model);
            } catch (Throwable) {
                continue;
            }

            if (! $relation instanceof Relation) {
                continue;
            }

            // MorphTo não possui um model de destino único sem o registro concreto.
            // O getRelated() retorna o model pai e criaria uma autorrelação falsa.
            if ($relation instanceof MorphTo) {
                continue;
            }

            $relatedSource = $this->sources->forModel($relation->getRelated()::class);
            if ($relatedSource === null) {
                continue;
            }

            $name = $method->getName();
            if (in_array($name, $excludedRelations, true)) {
                continue;
            }

            $relationColumns = $relatedSource->relationColumns($modelClass, $name);
            $relationColumns = $this->withoutFlattenedDuplicates(
                $relationColumns,
                $source,
                $relatedSource,
                $name,
            );
            $relationFilters = $this->withoutFlattenedDuplicates(
                $relatedSource->relationFilters($modelClass, $name),
                $source,
                $relatedSource,
                $name,
            );
            $pivot = $relation instanceof BelongsToMany ? $this->pivot($relation) : null;

            foreach ($pivot['filter_dtos'] ?? [] as $pivotFilter) {
                $relationFilters[] = $pivotFilter;
            }

            $relations[$name] = [
                'name' => $name,
                'type' => class_basename($relation),
                'label' => $relationLabels[$name] ?? $relatedSource->label(),
                'source' => $relatedSource,
                'columns' => collect($relationColumns)->mapWithKeys(
                    static fn (ReportColumnDTO $column): array => [$column->key => $column->label],
                )->all(),
                'column_dtos' => $relationColumns,
                'column_definitions' => array_map(
                    static fn (ReportColumnDTO $column): array => $column->toArray(),
                    $relationColumns,
                ),
                'filters' => collect($relationFilters)->mapWithKeys(
                    static fn (ReportFilterDefinitionDTO $filter): array => [$filter->key => $filter],
                )->all(),
                'pivot' => $pivot,
            ];
        }

        foreach ($source->polymorphicRelations() as $definition) {
            $name = $definition->key;
            $relatedSource = $this->sources->get($definition->sourceKey);
            $relationColumns = $relatedSource->relationColumns($modelClass, $definition->eloquentRelation);
            $relationFilters = $relatedSource->relationFilters($modelClass, $definition->eloquentRelation);
            $relationColumns = $this->withoutFlattenedDuplicates(
                $relationColumns,
                $source,
                $relatedSource,
                $definition->eloquentRelation,
            );
            $relationFilters = $this->withoutFlattenedDuplicates(
                $relationFilters,
                $source,
                $relatedSource,
                $definition->eloquentRelation,
            );

            $relations[$name] = [
                'name' => $name,
                'type' => 'MorphTo',
                'label' => $definition->label,
                'source' => $relatedSource,
                'eloquent_relation' => $definition->eloquentRelation,
                'morph_type' => $definition->morphType,
                'morph_class' => $relatedSource->modelClass(),
                'columns' => collect($relationColumns)->mapWithKeys(
                    static fn (ReportColumnDTO $column): array => [$column->key => $column->label],
                )->all(),
                'column_dtos' => $relationColumns,
                'column_definitions' => array_map(
                    static fn (ReportColumnDTO $column): array => $column->toArray(),
                    $relationColumns,
                ),
                'filters' => collect($relationFilters)->mapWithKeys(
                    static fn (ReportFilterDefinitionDTO $filter): array => [$filter->key => $filter],
                )->all(),
                'pivot' => null,
            ];
        }

        return $relations;
    }

    /**
     * @template T of ReportColumnDTO|ReportFilterDefinitionDTO
     *
     * @param  list<T>  $definitions
     * @return list<T>
     */
    private function withoutFlattenedDuplicates(
        array $definitions,
        ReportSource $parentSource,
        ReportSource $relatedSource,
        string $relationName,
    ): array {
        $prefix = $relationName.'.';
        $flattenedPaths = collect($parentSource->fieldPaths())
            ->filter(static fn (string $path): bool => str_starts_with($path, $prefix))
            ->map(static fn (string $path): string => substr($path, strlen($prefix)))
            ->all();
        $relatedPaths = $relatedSource->fieldPaths();
        $relatedModelClass = $relatedSource->modelClass();

        return array_values(array_filter(
            $definitions,
            function (ReportColumnDTO|ReportFilterDefinitionDTO $definition) use (
                $relatedPaths,
                $flattenedPaths,
                $relatedModelClass,
                $parentSource,
            ): bool {
                $path = $relatedPaths[$definition->key] ?? $definition->key;

                return ! in_array($path, $flattenedPaths, true)
                    && ! $this->pointsBackToParent($relatedModelClass, $parentSource->modelClass(), $path);
            },
        ));
    }

    /**
     * @param  class-string  $relatedModelClass
     * @param  class-string  $parentModelClass
     */
    private function pointsBackToParent(string $relatedModelClass, string $parentModelClass, string $path): bool
    {
        $relationName = explode('.', $path, 2)[0];
        $relatedModel = new $relatedModelClass;

        if (! method_exists($relatedModel, $relationName)) {
            return false;
        }

        try {
            $relation = $relatedModel->{$relationName}();
        } catch (Throwable) {
            return false;
        }

        return $relation instanceof Relation
            && ! $relation instanceof MorphTo
            && $relation->getRelated()::class === $parentModelClass;
    }

    /** @return array<string, mixed>|null */
    private function pivot(BelongsToMany $relation): ?array
    {
        $columns = array_values(array_diff($relation->getPivotColumns(), ['created_at', 'updated_at']));
        if ($columns === []) {
            return null;
        }

        $pivotSource = $this->sources->forModel($relation->getPivotClass());
        $labels = collect($pivotSource?->columns() ?? [])->mapWithKeys(
            static fn (ReportColumnDTO $column): array => [$column->key => $column->label],
        );

        $filterDTOs = collect($pivotSource?->filters() ?? [])
            ->filter(static fn (ReportFilterDefinitionDTO $filter): bool => in_array($filter->key, $columns, true))
            ->map(static fn (ReportFilterDefinitionDTO $filter): ReportFilterDefinitionDTO => new ReportFilterDefinitionDTO(
                key: 'pivot.'.$filter->key,
                label: 'Vínculo › '.$filter->label,
                type: $filter->type,
                operators: $filter->operators,
                options: $filter->options,
            ))
            ->values()
            ->all();

        return [
            'table' => $relation->getTable(),
            'columns' => collect($columns)->mapWithKeys(
                static fn (string $column): array => [
                    $column => $labels->get($column, str($column)->replace('_', ' ')->title()->toString()),
                ],
            )->all(),
            'filter_dtos' => $filterDTOs,
        ];
    }
}
