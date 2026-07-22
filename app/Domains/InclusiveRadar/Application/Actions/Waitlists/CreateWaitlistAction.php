<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Actions\Waitlists;

use App\Domains\InclusiveRadar\Application\Data\Waitlists\CreateWaitlistData;
use App\Domains\InclusiveRadar\Application\Handlers\Loans\LoanableLockHandler;
use App\Domains\InclusiveRadar\Application\Policies\Waitlists\WaitlistRegistrationPolicy;
use App\Domains\InclusiveRadar\Domain\DTOs\Waitlists\CreateWaitlistDTO;
use App\Domains\InclusiveRadar\Domain\Models\Waitlist;
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
                type: $data->waitlistableType,
                id: $data->waitlistableId,
            );

            $this->registrationPolicy->ensureCanRegister($item, $data);

            $waitlistDTO = new CreateWaitlistDTO(
                waitlistableId: $item->id,
                waitlistableType: $data->waitlistableType,
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
