<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Actions\Barriers;

use App\Domains\Inclusion\Domain\Models\Barrier;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class DeleteBarrierAction
{
    /**
     * @throws Throwable
     */
    public function execute(Barrier $barrier): void
    {
        DB::transaction(function () use ($barrier): void {
            $barrier->delete();
        });
    }
}
