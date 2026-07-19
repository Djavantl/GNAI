<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Queries\Loans;

use App\Domains\InclusiveRadar\Domain\Models\Loan;

final class LoanPdfQuery
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
