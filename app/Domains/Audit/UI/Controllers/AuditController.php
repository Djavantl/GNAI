<?php

declare(strict_types=1);

namespace App\Domains\Audit\UI\Controllers;

use App\Domains\Audit\Application\Data\ListAuditRecordsData;
use App\Domains\Audit\Application\Queries\AuditFilterOptionsQuery;
use App\Domains\Audit\Application\Queries\ListAuditRecordsQuery;
use App\Domains\Audit\Domain\Models\AuditRecord;
use App\Domains\Audit\UI\Presenters\AuditRecordPresenter;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

final class AuditController
{
    public function index(
        ListAuditRecordsData $filters,
        ListAuditRecordsQuery $records,
        AuditFilterOptionsQuery $options,
        AuditRecordPresenter $presenter,
    ): InertiaResponse {
        return Inertia::render('Audit/Index', [
            'records' => $records
                ->execute($filters)
                ->through(fn (AuditRecord $record): array => $presenter->present($record)),
            'filters' => $filters->toFilters(),
            'options' => $options->execute(),
        ]);
    }
}
