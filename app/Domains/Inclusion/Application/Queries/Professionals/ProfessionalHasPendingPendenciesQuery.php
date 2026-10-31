<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Queries\Professionals;

use App\Domains\Inclusion\Domain\Models\Professional;
use Illuminate\Support\Facades\DB;

final class ProfessionalHasPendingPendenciesQuery
{
    public function execute(Professional $professional): bool
    {
        return DB::table('pendencies')
            ->where('assigned_to', $professional->getKey())
            ->where('is_completed', false)
            ->exists();
    }
}
