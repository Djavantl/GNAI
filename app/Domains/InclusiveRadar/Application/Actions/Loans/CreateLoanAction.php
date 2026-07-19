<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Actions\Loans;

use App\Domains\InclusiveRadar\Application\Data\Loans\CreateLoanData;
use App\Domains\InclusiveRadar\Application\Handlers\Loans\LoanStockHandler;
use App\Domains\InclusiveRadar\Application\Handlers\Loans\LoanWaitlistHandler;
use App\Domains\InclusiveRadar\Application\Policies\Loans\LoanRegistrationPolicy;
use App\Domains\InclusiveRadar\Domain\DTOs\Loans\CreateLoanDTO;
use App\Domains\InclusiveRadar\Domain\Enums\LoanableType;
use App\Domains\InclusiveRadar\Domain\Exceptions\InvalidLoanableResource;
use App\Domains\InclusiveRadar\Domain\Models\AccessibleEducationalMaterial;
use App\Domains\InclusiveRadar\Domain\Models\AssistiveTechnology;
use App\Domains\InclusiveRadar\Domain\Models\Loan;
use Illuminate\Support\Facades\DB;

final readonly class CreateLoanAction
{
    public function __construct(
        private LoanRegistrationPolicy $registrationPolicy,
        private LoanStockHandler $stock,
        private LoanWaitlistHandler $waitlists,
    ) {}

    /**
     * @throws \Throwable
     */
    public function execute(CreateLoanData $data, int $registeredBy): Loan
    {
        $loan = DB::transaction(function () use ($data, $registeredBy): Loan {
            $item = $this->lockLoanable(
                type: $data->loanableType,
                id: $data->loanableId,
            );

            $this->registrationPolicy->ensureCanRegister(
                item: $item,
                data: $data,
            );
            $this->stock->withdraw($item);

            $loan = Loan::register(new CreateLoanDTO(
                loanableId: $item->id,
                loanableType: $data->loanableType,
                studentId: $data->studentId,
                professionalId: $data->professionalId,
                registeredBy: $registeredBy,
                loanDate: $data->loanDate,
                dueDate: $data->dueDate,
                observation: $data->observation,
            ));
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
