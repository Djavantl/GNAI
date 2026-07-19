<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Actions\Loans;

use App\Domains\InclusiveRadar\Application\Data\Loans\UpdateLoanData;
use App\Domains\InclusiveRadar\Domain\DTOs\Loans\UpdateLoanDTO;
use App\Domains\InclusiveRadar\Domain\Models\Loan;
use Illuminate\Support\Facades\DB;

final class UpdateLoanAction
{
    public function execute(Loan $loan, UpdateLoanData $data): Loan
    {
        $updatedLoan = DB::transaction(function () use ($loan, $data): Loan {
            $loan->revise(new UpdateLoanDTO(
                observation: $data->observation,
            ));
            $loan->save();

            return $loan;
        });

        return $updatedLoan->fresh([
            'loanable',
            'student.person',
            'professional.person',
            'user',
        ]);
    }
}
