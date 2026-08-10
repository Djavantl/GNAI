<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Actions\Loans;

use App\Domains\InclusiveRadar\Application\Data\Loans\ReturnLoanData;
use App\Domains\InclusiveRadar\Application\Handlers\Loans\LoanableLockHandler;
use App\Domains\InclusiveRadar\Application\Handlers\Loans\LoanStockHandler;
use App\Domains\InclusiveRadar\Application\Handlers\Loans\LoanWaitlistHandler;
use App\Domains\InclusiveRadar\Domain\DTOs\Loans\ReturnLoanDTO;
use App\Domains\InclusiveRadar\Domain\Enums\LoanableType;
use App\Domains\InclusiveRadar\Domain\Enums\LoanStatus;
use App\Domains\InclusiveRadar\Domain\Models\Loan;
use App\Domains\Auth\Domain\Models\User;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class ReturnLoanAction
{
    public function __construct(
        private LoanableLockHandler $loanables,
        private LoanStockHandler $stock,
        private LoanWaitlistHandler $waitlists,
    ) {}

    /**
     * @throws Throwable
     */
    public function execute(Loan $loan, ReturnLoanData $data, User $returnedBy): Loan
    {
        $returnedLoan = DB::transaction(function () use ($loan, $data, $returnedBy): Loan {
            $item = $this->loanables->lockForLoan(
                type: LoanableType::fromStored($loan->loanable_type),
                id: $loan->loanable_id,
            );

            $returnDate = now();
            $status = Loan::returnedStatus(
                isDamaged: $data->isDamaged,
                returnDate: $returnDate,
                dueDate: $loan->due_date,
            );

            $returnLoanDTO = new ReturnLoanDTO(
                returnDate: $returnDate,
                status: $status,
                observation: $data->observation,
            );

            $loan->registerReturn($returnLoanDTO);
            $loan->save();

            $this->stock->returnFromLoanStatus($item, $status);

            if ($status !== LoanStatus::DAMAGED) {
                $this->waitlists->notifyNext($item, $returnedBy);
            }

            return $loan;
        });

        return $returnedLoan->fresh([
            'loanable',
            'student.person',
            'professional.person',
            'user',
        ]);
    }
}
