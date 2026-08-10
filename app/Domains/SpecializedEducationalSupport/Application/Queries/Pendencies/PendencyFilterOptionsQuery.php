<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\Pendencies;

use App\Domains\SpecializedEducationalSupport\Domain\Models\Professional;
use Illuminate\Database\Eloquent\Collection;

final class PendencyFilterOptionsQuery
{
    /**
     * @return Collection<int, Professional>
     */
    public function professionals(): Collection
    {
        return Professional::query()
            ->with('person')
            ->orderBy('id')
            ->get();
    }
}
