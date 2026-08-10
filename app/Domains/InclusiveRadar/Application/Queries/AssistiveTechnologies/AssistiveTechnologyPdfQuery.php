<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Queries\AssistiveTechnologies;

use App\Domains\InclusiveRadar\Domain\Models\AssistiveTechnology;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

final class AssistiveTechnologyPdfQuery
{
    public function execute(
        AssistiveTechnology $technology,
    ): AssistiveTechnology {
        return $technology->load([
            'deficiencies' => static function (BelongsToMany $query): void {
                $query->orderBy('name');
            },
            'inspections' => static function (MorphMany $query): void {
                $query
                    ->with('evidences')
                    ->orderByDesc('inspection_date')
                    ->orderByDesc('created_at');
            },
        ]);
    }
}
