<?php

declare(strict_types=1);

namespace App\Domains\Reporting\Application\Queries;

use App\Domains\Reporting\Application\Services\ReportCatalog;
use App\Domains\Reporting\Application\Services\ReportRelationCatalog;
use App\Domains\Reporting\Domain\DTOs\ReportColumnDTO;
use App\Domains\Reporting\Domain\DTOs\ReportFilterDefinitionDTO;

final readonly class GetReportMetadataQuery
{
    public function __construct(
        private ReportCatalog $catalog,
        private ReportRelationCatalog $relations,
    ) {}

    /** @return array<string, mixed> */
    public function execute(string $sourceKey): array
    {
        $source = $this->catalog->get($sourceKey);

        $columns = $source->columns();
        $relations = collect($this->relations->for($source))->map(function (array $relation): array {
            $relation['filter_definitions'] = collect($relation['filters'])
                ->map(static fn (ReportFilterDefinitionDTO $filter): array => $filter->toArray())
                ->values()
                ->all();
            unset($relation['pivot']['filter_dtos']);
            unset($relation['source'], $relation['filters'], $relation['column_dtos']);

            return $relation;
        })->values()->all();

        return [
            'key' => $source->key(),
            'label' => $source->label(),
            'columns' => collect($columns)->mapWithKeys(
                static fn (ReportColumnDTO $column): array => [$column->key => $column->label],
            )->all(),
            'column_definitions' => array_map(
                static fn (ReportColumnDTO $column): array => $column->toArray(),
                $columns,
            ),
            'filters' => array_map(
                static fn (ReportFilterDefinitionDTO $filter): array => $filter->toArray(),
                $source->filters(),
            ),
            'relations' => $relations,
        ];
    }
}
