<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Queries\Waitlists;

use App\Domains\Inclusion\Domain\Enums\WaitlistStatus;
use App\Domains\Inclusion\Domain\Models\Item;
use App\Domains\Inclusion\Domain\Models\Waitlist;
use Illuminate\Database\Eloquent\Builder;

final class ActiveWaitlistExistsQuery
{
    public function execute(
        int $waitlistableId,
        ?int $studentId,
        ?int $professionalId,
    ): bool {
        return Waitlist::query()
            ->where('waitlistable_id', $waitlistableId)
            ->where('waitlistable_type', (new Item)->getMorphClass())
            ->whereIn('status', [
                WaitlistStatus::WAITING->value,
                WaitlistStatus::NOTIFIED->value,
            ])
            ->where(function (Builder $query) use ($studentId, $professionalId): void {
                if ($studentId !== null) {
                    $query->where('student_id', $studentId);

                    return;
                }

                $query->where('professional_id', $professionalId);
            })
            ->exists();
    }
}
