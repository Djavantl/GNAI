<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Actions\Pendencies;

use App\Domains\Inclusion\Domain\Models\Pendency;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class DeletePendencyAction
{
    /**
     * @throws Throwable
     */
    public function execute(Pendency $pendency): void
    {
        DB::transaction(function () use ($pendency): void {
            Pendency::query()
                ->findOrFail($pendency->getKey())
                ->delete();
        });
    }
}
