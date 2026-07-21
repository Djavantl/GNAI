<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Actions\AssistiveTechnologies;

use App\Domains\InclusiveRadar\Domain\Exceptions\ResourceHasOpenLoans;
use App\Domains\InclusiveRadar\Domain\Models\AssistiveTechnology;
use Illuminate\Support\Facades\DB;

final readonly class DeleteAssistiveTechnologyAction
{
    /**
     * @throws \Throwable
     */
    public function execute(AssistiveTechnology $technology): void
    {
        DB::transaction(function () use ($technology): void {
            $lockedTechnology = AssistiveTechnology::query()
                ->lockForUpdate()
                ->findOrFail($technology->getKey());

            $hasOpenLoans = $lockedTechnology->loans()
                ->whereNull('return_date')
                ->exists();

            if ($hasOpenLoans) {
                throw new ResourceHasOpenLoans;
            }

            $lockedTechnology->delete();
        });
    }
}
