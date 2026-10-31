<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Policies\Waitlists;

use App\Domains\Inclusion\Application\Data\Waitlists\CreateWaitlistData;
use App\Domains\Inclusion\Application\Queries\Loans\OpenLoanExistsQuery;
use App\Domains\Inclusion\Application\Queries\Waitlists\ActiveWaitlistExistsQuery;
use App\Domains\Inclusion\Domain\Exceptions\InvalidWaitlist;
use App\Domains\Inclusion\Domain\Models\Item;

final readonly class WaitlistRegistrationPolicy
{
    public function __construct(
        private ActiveWaitlistExistsQuery $activeWaitlistExists,
        private OpenLoanExistsQuery $openLoanExists,
    ) {}

    /**
     * @throws InvalidWaitlist
     */
    public function ensureCanRegister(
        Item $item,
        CreateWaitlistData $data,
    ): void {
        $this->ensureSingleBeneficiary($data);
        $this->ensureItemIsUnavailable($item);
        $this->ensureNoDuplicateWaitlist($item, $data);
        $this->ensureNoOpenLoan($item, $data);
    }

    /**
     * @throws InvalidWaitlist
     */
    private function ensureSingleBeneficiary(CreateWaitlistData $data): void
    {
        if ($data->studentId === null && $data->professionalId === null) {
            throw new InvalidWaitlist('É necessário informar um aluno ou um profissional.');
        }

        if ($data->studentId !== null && $data->professionalId !== null) {
            throw new InvalidWaitlist('Não é permitido informar aluno e profissional ao mesmo tempo.');
        }
    }

    /**
     * @throws InvalidWaitlist
     */
    private function ensureItemIsUnavailable(Item $item): void
    {
        if ($item->is_digital) {
            throw new InvalidWaitlist(
                'Recursos digitais não entram em fila de espera porque não possuem estoque físico.'
            );
        }

        if (! $item->status->blocksLoan() && $item->quantity_available > 0) {
            throw new InvalidWaitlist(
                'Este recurso ainda possui unidades disponíveis e pode ser emprestado, portanto não é possível criar uma fila de espera.'
            );
        }
    }

    /**
     * @throws InvalidWaitlist
     */
    private function ensureNoDuplicateWaitlist(
        Item $item,
        CreateWaitlistData $data,
    ): void {
        if ($this->activeWaitlistExists->execute(
            waitlistableId: $item->id,
            studentId: $data->studentId,
            professionalId: $data->professionalId,
        )) {
            throw new InvalidWaitlist('Este beneficiário já possui uma solicitação ativa para este recurso.');
        }
    }

    /**
     * @throws InvalidWaitlist
     */
    private function ensureNoOpenLoan(
        Item $item,
        CreateWaitlistData $data,
    ): void {
        if ($this->openLoanExists->execute(
            loanableId: $item->id,
            studentId: $data->studentId,
            professionalId: $data->professionalId,
        )) {
            throw new InvalidWaitlist('Este beneficiário já possui um empréstimo ativo deste recurso.');
        }
    }
}
