<?php

declare(strict_types=1);

namespace App\Domains\Audit\Application\Queries;

use App\Domains\Audit\Application\Data\ListAuditRecordsData;
use App\Domains\Audit\Domain\Models\AuditRecord;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

/** Histórico do sistema, do mais recente ao mais antigo. */
final class ListAuditRecordsQuery
{
    /**
     * @return LengthAwarePaginator<int, AuditRecord>
     */
    public function execute(ListAuditRecordsData $filters): LengthAwarePaginator
    {
        $query = AuditRecord::query()->latest('id');

        // Intervalo na própria coluna (e não DATE(created_at)): assim o índice de created_at é usado.
        if ($filters->dateFrom !== null) {
            $query->where('created_at', '>=', CarbonImmutable::parse($filters->dateFrom)->startOfDay());
        }

        if ($filters->dateTo !== null) {
            $query->where('created_at', '<', CarbonImmutable::parse($filters->dateTo)->addDay()->startOfDay());
        }

        if ($filters->actor === ListAuditRecordsData::SYSTEM_ACTOR) {
            // Sem nome também: o autor excluído depois perde o id, mas não deixa de ser uma pessoa.
            $query->whereNull('actor_id')->whereNull('actor_name');
        } elseif ($filters->actor !== null) {
            $query->where('actor_id', (int) $filters->actor);
        }

        if ($filters->type !== null) {
            $query->where('entity_type', $filters->type);
        }

        if ($filters->event !== null) {
            $query->where('event', $filters->event);
        }

        if ($filters->search !== null) {
            $term = '%'.addcslashes($filters->search, '\\%_').'%';

            $query->where(fn (Builder $query) => $query
                ->where('entity_description', 'like', $term)
                ->orWhere('actor_name', 'like', $term)
                ->orWhere('impersonator_name', 'like', $term));
        }

        return $query
            ->paginate($filters->perPage)
            ->withQueryString();
    }
}
