<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Policies\Loans;

use App\Domains\Inclusion\Application\Data\Loans\CreateLoanData;
use App\Domains\Inclusion\Application\Queries\Loans\OpenLoanExistsQuery;
use App\Domains\Inclusion\Domain\Exceptions\InvalidLoan;
use App\Domains\Inclusion\Domain\Models\Item;

final readonly class LoanRegistrationPolicy
{
    public function __construct(
        private OpenLoanExistsQuery $openLoanExists,
    ) {}

    /**
     * @throws InvalidLoan
     */
    public function ensureCanRegister(
        Item $item,
        CreateLoanData $data,
    ): void {
        $this->ensureValidBeneficiary(
            studentId: $data->studentId,
            professionalId: $data->professionalId,
        );
        $this->ensureResourceCanBeLoaned($item);
        $this->ensureBeneficiaryHasNoOpenLoan($data);
    }

    /**
     * @throws InvalidLoan
     */
    private function ensureValidBeneficiary(?int $studentId, ?int $professionalId): void
    {
        if ($studentId === null && $professionalId === null) {
            throw new InvalidLoan('É necessário informar um aluno ou um profissional.');
        }

        if ($studentId !== null && $professionalId !== null) {
            throw new InvalidLoan('Não é permitido informar aluno e profissional ao mesmo tempo.');
        }
    }

    /**
     * @throws InvalidLoan
     */
    private function ensureResourceCanBeLoaned(Item $item): void
    {
        if (! $item->is_active || ! $item->is_loanable) {
            throw new InvalidLoan('Este recurso não está disponível para empréstimo.');
        }

        if ($item->status->blocksLoan()) {
            throw new InvalidLoan("O recurso está com status '{$item->status->label()}', que bloqueia empréstimos.");
        }

        if ($item->is_digital) {
            return;
        }

        if ($item->conservation_state?->blocksLoan()) {
            throw new InvalidLoan("O estado '{$item->conservation_state->label()}' bloqueia empréstimos.");
        }

        if ($item->stock()->isEmpty()) {
            throw new InvalidLoan('Não há unidades disponíveis para empréstimo.');
        }
    }

    /**
     * @throws InvalidLoan
     */
    private function ensureBeneficiaryHasNoOpenLoan(CreateLoanData $data): void
    {
        if (! $this->openLoanExists->execute(
            loanableId: $data->loanableId,
            studentId: $data->studentId,
            professionalId: $data->professionalId,
        )) {
            return;
        }

        throw new InvalidLoan('Este beneficiário já possui um empréstimo ativo deste recurso.');
    }
}
