<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Queries\Barriers;

use App\Domains\InclusiveRadar\Domain\Models\Barrier;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

final class ShowBarrierQuery
{
    public function execute(Barrier $barrier): Barrier
    {
        return $barrier->load([
            'category',
            'location',
            'institution',
            'deficiencies' => static function (BelongsToMany $query): void {
                $query->orderBy('name');
            },
            'inspections' => static function (MorphMany $query): void {
                $query
                    ->with('images')
                    ->orderByDesc('inspection_date')
                    ->orderByDesc('created_at');
            },
            'registeredBy',
            'affectedStudent.person',
            'affectedProfessional.person',
        ]);
    }
}
