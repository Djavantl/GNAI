<?php

declare(strict_types=1);

namespace App\Domains\Reporting\Application\Contracts;

use App\Domains\Reporting\Domain\DTOs\PolymorphicReportRelationDTO;
use App\Domains\Reporting\Domain\DTOs\ReportColumnDTO;
use App\Domains\Reporting\Domain\DTOs\ReportFilterDefinitionDTO;
use App\Domains\Reporting\Domain\DTOs\ReportRequestDTO;
use App\Domains\Reporting\Domain\Enums\ReportSourceVisibility;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

interface ReportSource
{
    /** @return class-string<Model> */
    public function modelClass(): string;

    /** @return list<string> */
    public function eagerLoads(): array;

    /** @return array<string, string> */
    public function relationLabels(): array;

    /** @return list<string> */
    public function excludedRelations(): array;

    /** @return list<PolymorphicReportRelationDTO> */
    public function polymorphicRelations(): array;

    public function key(): string;

    public function label(): string;

    public function visibility(): ReportSourceVisibility;

    /** @return list<ReportColumnDTO> */
    public function columns(): array;

    /** @return array<string, string> */
    public function fieldPaths(): array;

    /** @return list<ReportColumnDTO> */
    public function relationColumns(string $parentModel, string $relationName): array;

    /** @return list<ReportFilterDefinitionDTO> */
    public function filters(): array;

    /** @return list<ReportFilterDefinitionDTO> */
    public function relationFilters(string $parentModel, string $relationName): array;

    /** @return Builder<Model> */
    public function query(ReportRequestDTO $request): Builder;

    public function applyFilter(Builder $query, string $field, string $operator, mixed $value): void;

    public function value(Model $model, string $column): mixed;
}
