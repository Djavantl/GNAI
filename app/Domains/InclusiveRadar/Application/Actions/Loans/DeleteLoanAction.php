<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Actions\Loans;

use App\Domains\InclusiveRadar\Application\Handlers\Loans\LoanableLockHandler;
use App\Domains\InclusiveRadar\Application\Handlers\Loans\LoanStockHandler;
use App\Domains\InclusiveRadar\Application\Handlers\Loans\LoanWaitlistHandler;
use App\Domains\InclusiveRadar\Domain\Enums\LoanableType;
use App\Domains\InclusiveRadar\Domain\Models\Loan;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class DeleteLoanAction
{
    public function __construct(
        private LoanableLockHandler $loanables,
        private LoanStockHandler $stock,
        private LoanWaitlistHandler $waitlists,
    ) {}

    /**
     * @throws Throwable
     */
    public function execute(Loan $loan, User $deletedBy): void
    {
        DB::transaction(function () use ($loan, $deletedBy): void {
            if ($loan->return_date === null) {
                $item = $this->loanables->lockForLoan(
                    type: LoanableType::fromStored($loan->loanable_type),
                    id: $loan->loanable_id,
                );

                $this->stock->returnAvailable($item);
                $this->waitlists->notifyNext($item, $deletedBy);
            }

            $loan->delete();
        });
    }
}
