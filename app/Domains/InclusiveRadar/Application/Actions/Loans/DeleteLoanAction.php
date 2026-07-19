<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Actions\Loans;

use App\Domains\InclusiveRadar\Application\Handlers\Loans\LoanStockHandler;
use App\Domains\InclusiveRadar\Application\Handlers\Loans\LoanWaitlistHandler;
use App\Domains\InclusiveRadar\Domain\Enums\LoanableType;
use App\Domains\InclusiveRadar\Domain\Exceptions\InvalidLoanableResource;
use App\Domains\InclusiveRadar\Domain\Models\AccessibleEducationalMaterial;
use App\Domains\InclusiveRadar\Domain\Models\AssistiveTechnology;
use App\Domains\InclusiveRadar\Domain\Models\Loan;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final readonly class DeleteLoanAction
{
    public function __construct(
        private LoanStockHandler $stock,
        private LoanWaitlistHandler $waitlists,
    ) {}

    public function execute(Loan $loan, User $deletedBy): void
    {
        DB::transaction(function () use ($loan, $deletedBy): void {
            if ($loan->return_date === null) {
                $item = $this->lockLoanable(
                    type: LoanableType::fromStored($loan->loanable_type),
                    id: $loan->loanable_id,
                );

                $this->stock->returnAvailable($item);
                $this->waitlists->notifyNext($item, $deletedBy);
            }

            $loan->delete();
        });
    }

    private function lockLoanable(LoanableType $type, int $id): AccessibleEducationalMaterial|AssistiveTechnology
    {
        $model = $type->modelClass();

        /** @var AccessibleEducationalMaterial|AssistiveTechnology|null $item */
        $item = $model::query()
            ->lockForUpdate()
            ->find($id);

        if (
            ! $item instanceof AccessibleEducationalMaterial
            && ! $item instanceof AssistiveTechnology
        ) {
            throw new InvalidLoanableResource('O item para empréstimo não foi encontrado.');
        }

        return $item;
    }
}
