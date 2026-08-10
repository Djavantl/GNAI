<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Handlers\Loans;

use App\Domains\InclusiveRadar\Domain\Enums\LoanableType;
use App\Domains\InclusiveRadar\Domain\Enums\WaitlistStatus;
use App\Domains\InclusiveRadar\Domain\Models\AccessibleEducationalMaterial;
use App\Domains\InclusiveRadar\Domain\Models\AssistiveTechnology;
use App\Domains\InclusiveRadar\Domain\Models\Waitlist;
use App\Domains\InclusiveRadar\Infrastructure\Notifications\LoanableItemAvailableNotification;
use App\Domains\Auth\Domain\Models\User;

final class LoanWaitlistHandler
{
    public function fulfillMatching(
        AccessibleEducationalMaterial|AssistiveTechnology $item,
        ?int $studentId,
        ?int $professionalId,
    ): void {
        $query = Waitlist::query()
            ->where('waitlistable_id', $item->id)
            ->where('waitlistable_type', LoanableType::fromModel($item)->value)
            ->whereIn('status', [
                WaitlistStatus::WAITING->value,
                WaitlistStatus::NOTIFIED->value,
            ]);

        if ($studentId !== null) {
            $query->where('student_id', $studentId);
        }

        if ($professionalId !== null) {
            $query->where('professional_id', $professionalId);
        }

        $waitlist = $query->first();

        if ($waitlist instanceof Waitlist) {
            $waitlist->update([
                'status' => WaitlistStatus::FULFILLED,
            ]);
        }
    }

    public function notifyNext(AccessibleEducationalMaterial|AssistiveTechnology $item, User $notifier): ?Waitlist
    {
        $waitlist = Waitlist::query()
            ->where('waitlistable_id', $item->id)
            ->where('waitlistable_type', LoanableType::fromModel($item)->value)
            ->where('status', WaitlistStatus::WAITING->value)
            ->oldest('requested_at')
            ->first();

        if (! $waitlist instanceof Waitlist) {
            return null;
        }

        $waitlist->update([
            'status' => WaitlistStatus::NOTIFIED,
        ]);

        $waitlist->loadMissing([
            'waitlistable',
            'student.person',
            'professional.person',
        ]);

        $notifier->notify(new LoanableItemAvailableNotification($waitlist));

        return $waitlist;
    }
}
