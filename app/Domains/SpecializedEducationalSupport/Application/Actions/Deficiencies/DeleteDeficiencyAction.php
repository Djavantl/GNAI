<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Actions\Deficiencies;

use App\Domains\SpecializedEducationalSupport\Application\Queries\Deficiencies\DeficiencyHasStudentsQuery;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Deficiency;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class DeleteDeficiencyAction
{
    public function __construct(
        private DeficiencyHasStudentsQuery $hasStudents,
    ) {}

    /**
     * @throws Throwable
     */
    public function execute(Deficiency $deficiency): void
    {
        DB::transaction(function () use ($deficiency): void {
            $lockedDeficiency = Deficiency::query()
                ->lockForUpdate()
                ->findOrFail($deficiency->getKey());

            $lockedDeficiency->ensureCanBeDeactivated(
                $this->hasStudents->execute($lockedDeficiency),
            );

            $lockedDeficiency->delete();
        });
    }
}
