<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Actions\Loans;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\Inclusion\Application\Handlers\Loans\LoanableLockHandler;
use App\Domains\Inclusion\Application\Handlers\Loans\LoanStockHandler;
use App\Domains\Inclusion\Application\Handlers\Loans\LoanWaitlistHandler;
use App\Domains\Inclusion\Domain\Models\Loan;
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
                    id: $loan->loanable_id,
                );

                $this->stock->returnAvailable($item);
                $this->waitlists->notifyNext($item, $deletedBy);
            }

            $loan->delete();
        });
    }
}
