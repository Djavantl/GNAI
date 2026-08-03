<?php

declare(strict_types=1);

namespace App\Domains\Reporting\Application\Queries;

use App\Domains\Reporting\Application\Contracts\ReportSource;
use App\Domains\Reporting\Application\Services\ReportCatalog;
use App\Domains\Reporting\Domain\Enums\ReportSourceVisibility;

final readonly class ListReportSourcesQuery
{
    public function __construct(private ReportCatalog $catalog) {}

    /** @return list<array{key: string, label: string}> */
    public function execute(): array
    {
        return $this->catalog->all()
            ->filter(static fn (ReportSource $source): bool => $source->visibility() === ReportSourceVisibility::PRIMARY)
            ->values()
            ->map(fn (ReportSource $source): array => [
                'key' => $source->key(),
                'label' => $source->label(),
            ])
            ->all();
    }
}
