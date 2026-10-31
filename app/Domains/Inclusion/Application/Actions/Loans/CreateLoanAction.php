<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Actions\Loans;

use App\Domains\Inclusion\Application\Data\Loans\CreateLoanData;
use App\Domains\Inclusion\Application\Handlers\Loans\LoanableLockHandler;
use App\Domains\Inclusion\Application\Handlers\Loans\LoanStockHandler;
use App\Domains\Inclusion\Application\Handlers\Loans\LoanWaitlistHandler;
use App\Domains\Inclusion\Application\Policies\Loans\LoanRegistrationPolicy;
use App\Domains\Inclusion\Domain\DTOs\Loans\CreateLoanDTO;
use App\Domains\Inclusion\Domain\Models\Loan;
use Illuminate\Support\Facades\DB;

final readonly class CreateLoanAction
{
    public function __construct(
        private LoanRegistrationPolicy $registrationPolicy,
        private LoanableLockHandler $loanables,
        private LoanStockHandler $stock,
        private LoanWaitlistHandler $waitlists,
    ) {}

    /**
     * @throws \Throwable
     */
    public function execute(CreateLoanData $data, int $registeredBy): Loan
    {
        $loan = DB::transaction(function () use ($data, $registeredBy): Loan {
            $item = $this->loanables->lockForLoan(
                id: $data->loanableId,
                expectedType: $data->itemType,
            );

            $this->registrationPolicy->ensureCanRegister(
                item: $item,
                data: $data,
            );
            $this->stock->withdraw($item);

            $loanDTO = new CreateLoanDTO(
                loanableId: $item->id,
                loanableType: $item->getMorphClass(),
                studentId: $data->studentId,
                professionalId: $data->professionalId,
                registeredBy: $registeredBy,
                loanDate: $data->loanDate,
                dueDate: $data->dueDate,
                observation: $data->observation,
            );

            $loan = Loan::register($loanDTO);
            $loan->save();

            $this->waitlists->fulfillMatching(
                item: $item,
                studentId: $data->studentId,
                professionalId: $data->professionalId,
            );

            return $loan;
        });

        return $loan->fresh([
            'loanable',
            'student.person',
            'professional.person',
            'user',
        ]);
    }
}
