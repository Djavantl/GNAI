<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Queries\Waitlists;

use App\Domains\InclusiveRadar\Domain\Enums\LoanableType;
use App\Domains\InclusiveRadar\Domain\Enums\WaitlistStatus;
use App\Domains\InclusiveRadar\Domain\Models\Waitlist;
use Illuminate\Database\Eloquent\Builder;

final class ActiveWaitlistExistsQuery
{
    public function execute(
        int $waitlistableId,
        LoanableType $waitlistableType,
        ?int $studentId,
        ?int $professionalId,
    ): bool {
        return Waitlist::query()
            ->where('waitlistable_id', $waitlistableId)
            ->where('waitlistable_type', $waitlistableType->value)
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
