<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Actions\Loans;

use App\Domains\InclusiveRadar\Application\Data\Loans\UpdateLoanData;
use App\Domains\InclusiveRadar\Domain\DTOs\Loans\UpdateLoanDTO;
use App\Domains\InclusiveRadar\Domain\Models\Loan;

final class UpdateLoanAction
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
