<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Queries\Loans;

use App\Domains\Inclusion\Domain\Models\Loan;

final class ShowLoanQuery
{
    public function execute(Loan $loan): Loan
    {
        return $loan->load([
            'loanable',
            'student:id,person_id,registration',
            'student.person:id,name',
            'professional:id,person_id,registration',
            'professional.person:id,name',
            'user:id,name',
        ]);
    }
}
