<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\Disciplines;

use App\Domains\SpecializedEducationalSupport\Domain\Models\Discipline;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

final class ShowDisciplineQuery
{
    public function execute(Discipline $discipline): Discipline
    {
        return $discipline->load([
            'courses' => static function (BelongsToMany $query): void {
                $query->orderBy('name');
            },
        ]);
    }
}
