<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Handlers\Loans;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\Inclusion\Domain\Enums\WaitlistStatus;
use App\Domains\Inclusion\Domain\Models\Item;
use App\Domains\Inclusion\Domain\Models\Waitlist;
use App\Domains\Inclusion\Infrastructure\Notifications\LoanableItemAvailableNotification;

final class LoanWaitlistHandler
{
    public function fulfillMatching(
        Item $item,
        ?int $studentId,
        ?int $professionalId,
    ): void {
        $query = Waitlist::query()
            ->where('waitlistable_id', $item->id)
            ->where('waitlistable_type', $item->getMorphClass())
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

    public function notifyNext(Item $item, User $notifier): ?Waitlist
    {
        $waitlist = Waitlist::query()
            ->where('waitlistable_id', $item->id)
            ->where('waitlistable_type', $item->getMorphClass())
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
