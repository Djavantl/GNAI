<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Actions\Pendencies;

use App\Domains\SpecializedEducationalSupport\Domain\Models\Pendency;
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
