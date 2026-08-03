<?php

declare(strict_types=1);

namespace App\Domains\Reporting\Infrastructure\Sources;

use App\Domains\Reporting\Application\Contracts\ReportSource;
use App\Domains\Reporting\Domain\DTOs\ReportColumnDTO;
use App\Domains\Reporting\Domain\DTOs\ReportFilterDefinitionDTO;
use App\Domains\Reporting\Domain\DTOs\ReportRequestDTO;
use App\Domains\Reporting\Domain\Enums\ReportColumnType;
use App\Domains\Reporting\Domain\Enums\ReportOperator;
use App\Domains\Reporting\Domain\Enums\ReportSourceVisibility;
use App\Domains\Reporting\Domain\Exceptions\ReportingException;
use BackedEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

abstract class EloquentReportSource implements ReportSource
{
    /** @return class-string<Model> */
    abstract protected function model(): string;

    /**
     * @return array<string, array{
     *     label: string,
     *     path?: string,
     *     type?: ReportColumnType,
     *     options?: array<string, string>
     * }>
     */
    abstract protected function definitions(): array;

    /** @return list<string> */
    abstract protected function filterable(): array;

    public function modelClass(): string
    {
        return $this->model();
    }

    public function eagerLoads(): array
    {
        return $this->with();
    }

    public function relationLabels(): array
    {
        return [];
    }

    public function excludedRelations(): array
    {
        return [];
    }

    public function polymorphicRelations(): array
    {
        return [];
    }

    public function visibility(): ReportSourceVisibility
    {
        return ReportSourceVisibility::PRIMARY;
    }

    /** @return list<string> */
    protected function with(): array
    {
        return [];
    }

    public function columns(): array
    {
        return collect($this->definitions())
            ->map(fn (array $definition, string $key): ReportColumnDTO => new ReportColumnDTO(
                key: $key,
                label: $definition['label'],
                type: $definition['type'] ?? ReportColumnType::TEXT,
                options: $definition['options'] ?? [],
            ))
            ->values()
            ->all();
    }

    public function fieldPaths(): array
    {
        return collect($this->definitions())
            ->mapWithKeys(static fn (array $definition, string $key): array => [
                $key => $definition['path'] ?? $key,
            ])
            ->all();
    }

    public function relationColumns(string $parentModel, string $relationName): array
    {
        return $this->applyRelationPolicy($this->columns(), $parentModel, $relationName);
    }

    public function filters(): array
    {
        $definitions = $this->definitions();

        return array_map(/**
         * @throws ReportingException
         */ function (string $key) use ($definitions): ReportFilterDefinitionDTO {
            $definition = $definitions[$key]
                ?? throw new ReportingException("O filtro {$key} não possui uma coluna correspondente.");
            $type = $definition['type'] ?? ReportColumnType::TEXT;

            return new ReportFilterDefinitionDTO(
                key: $key,
                label: $definition['label'],
                type: $type,
                operators: $this->operatorsFor($type),
                options: $definition['options'] ?? [],
            );
        }, $this->filterable());
    }

    /**
     * @throws ReportingException
     */
    public function relationFilters(string $parentModel, string $relationName): array
    {
        return $this->applyRelationPolicy($this->filters(), $parentModel, $relationName);
    }

    /**
     * @return array{include?: list<string>, exclude?: list<string>}
     */
    protected function relationPolicy(string $parentModel, string $relationName): array
    {
        return [];
    }

    /**
     * @template T of ReportColumnDTO|ReportFilterDefinitionDTO
     *
     * @param  list<T>  $definitions
     * @return list<T>
     */
    private function applyRelationPolicy(array $definitions, string $parentModel, string $relationName): array
    {
        $policy = $this->relationPolicy($parentModel, $relationName);
        $include = $policy['include'] ?? null;
        $exclude = $policy['exclude'] ?? [];

        return array_values(array_filter(
            $definitions,
            static fn (ReportColumnDTO|ReportFilterDefinitionDTO $definition): bool => ($include === null
                || in_array($definition->key, $include, true))
                && ! in_array($definition->key, $exclude, true),
        ));
    }

    public function query(ReportRequestDTO $request): Builder
    {
        $model = $this->model();
        $query = $model::query();

        if ($this->with() !== []) {
            $query->with($this->with());
        }

        return $query->orderBy((new $model)->getQualifiedKeyName());
    }

    /**
     * @throws ReportingException
     */
    public function applyFilter(Builder $query, string $field, string $operator, mixed $value): void
    {
        $definition = $this->definitions()[$field]
            ?? throw new ReportingException("O filtro {$field} não é suportado por esta fonte.");
        $path = $definition['path'] ?? $field;
        $segments = explode('.', $path);
        $column = array_pop($segments);
        $value = is_string($value) ? trim($value) : $value;

        if (($definition['type'] ?? null) === ReportColumnType::BOOLEAN) {
            $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }

        if ($segments === []) {
            $this->where($query, $column, $operator, $value);

            return;
        }

        $relation = implode('.', $segments);
        $query->whereHas(
            $relation,
            fn (Builder $related): Builder => $this->where($related, $column, $operator, $value),
        );
    }

    /**
     * @throws ReportingException
     */
    public function value(Model $model, string $column): mixed
    {
        $definition = $this->definitions()[$column]
            ?? throw new ReportingException("A coluna {$column} não é suportada por esta fonte.");

        return data_get($model, $definition['path'] ?? $column);
    }

    /**
     * @param  class-string<BackedEnum>  $enum
     * @return array<string, string>
     */
    protected function enumOptions(string $enum): array
    {
        return collect($enum::cases())
            ->mapWithKeys(static fn (BackedEnum $case): array => [
                (string) $case->value => method_exists($case, 'label') ? $case->label() : (string) $case->value,
            ])
            ->all();
    }

    /** @return list<ReportOperator> */
    private function operatorsFor(ReportColumnType $type): array
    {
        return match ($type) {
            ReportColumnType::TEXT => [ReportOperator::CONTAINS, ReportOperator::EQUALS],
            ReportColumnType::DATE => [
                ReportOperator::EQUALS,
                ReportOperator::GREATER_THAN_OR_EQUALS,
                ReportOperator::LESS_THAN_OR_EQUALS,
            ],
            ReportColumnType::BOOLEAN => [ReportOperator::EQUALS],
            ReportColumnType::SELECT => [ReportOperator::EQUALS, ReportOperator::NOT_EQUALS],
        };
    }

    private function where(Builder $query, string $column, string $operator, mixed $value): Builder
    {
        return $operator === 'like'
            ? $query->where($column, 'like', '%'.$value.'%')
            : $query->where($column, $operator, $value);
    }
}
