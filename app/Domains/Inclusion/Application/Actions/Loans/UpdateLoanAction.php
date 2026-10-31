<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Actions\Loans;

use App\Domains\Inclusion\Application\Data\Loans\UpdateLoanData;
use App\Domains\Inclusion\Domain\DTOs\Loans\UpdateLoanDTO;
use App\Domains\Inclusion\Domain\Models\Loan;

final readonly class UpdateLoanAction
{
    public function execute(Loan $loan, UpdateLoanData $data): Loan
    {
        $loanDTO = new UpdateLoanDTO(
            observation: $data->observation,
        );

        $loan->revise($loanDTO);
        $loan->save();

        return $loan->fresh([
            'loanable',
            'student.person',
            'professional.person',
            'user',
        ]);
    }
}
