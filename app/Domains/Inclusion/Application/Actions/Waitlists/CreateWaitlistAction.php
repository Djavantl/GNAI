<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Actions\Waitlists;

use App\Domains\Inclusion\Application\Data\Waitlists\CreateWaitlistData;
use App\Domains\Inclusion\Application\Handlers\Loans\LoanableLockHandler;
use App\Domains\Inclusion\Application\Policies\Waitlists\WaitlistRegistrationPolicy;
use App\Domains\Inclusion\Domain\DTOs\Waitlists\CreateWaitlistDTO;
use App\Domains\Inclusion\Domain\Models\Waitlist;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class CreateWaitlistAction
{
    public function __construct(
        private LoanableLockHandler $loanables,
        private WaitlistRegistrationPolicy $registrationPolicy,
    ) {}

    /**
     * @throws Throwable
     */
    public function execute(CreateWaitlistData $data, int $registeredBy): Waitlist
    {
        $waitlist = DB::transaction(function () use ($data, $registeredBy): Waitlist {
            $item = $this->loanables->lockForLoan(
                id: $data->waitlistableId,
                expectedType: $data->itemType,
            );

            $this->registrationPolicy->ensureCanRegister($item, $data);

            $waitlistDTO = new CreateWaitlistDTO(
                waitlistableId: $item->id,
                waitlistableType: $item->getMorphClass(),
                studentId: $data->studentId,
                professionalId: $data->professionalId,
                registeredBy: $registeredBy,
                requestedAt: now(),
                observation: $data->observation,
            );

            $waitlist = Waitlist::register($waitlistDTO);
            $waitlist->save();

            return $waitlist;
        });

        return $waitlist->fresh([
            'waitlistable',
            'student.person',
            'professional.person',
            'user',
        ]);
    }
}
