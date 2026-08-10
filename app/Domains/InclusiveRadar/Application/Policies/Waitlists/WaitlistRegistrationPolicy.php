<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Policies\Waitlists;

use App\Domains\InclusiveRadar\Application\Data\Waitlists\CreateWaitlistData;
use App\Domains\InclusiveRadar\Application\Queries\Loans\OpenLoanExistsQuery;
use App\Domains\InclusiveRadar\Application\Queries\Waitlists\ActiveWaitlistExistsQuery;
use App\Domains\InclusiveRadar\Domain\Enums\LoanableType;
use App\Domains\InclusiveRadar\Domain\Exceptions\InvalidWaitlist;
use App\Domains\InclusiveRadar\Domain\Models\AccessibleEducationalMaterial;
use App\Domains\InclusiveRadar\Domain\Models\AssistiveTechnology;

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
        AccessibleEducationalMaterial|AssistiveTechnology $item,
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
    private function ensureItemIsUnavailable(AccessibleEducationalMaterial|AssistiveTechnology $item): void
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
        AccessibleEducationalMaterial|AssistiveTechnology $item,
        CreateWaitlistData $data,
    ): void {
        if ($this->activeWaitlistExists->execute(
            waitlistableId: $item->id,
            waitlistableType: LoanableType::fromModel($item),
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
        AccessibleEducationalMaterial|AssistiveTechnology $item,
        CreateWaitlistData $data,
    ): void {
        if ($this->openLoanExists->execute(
            loanableId: $item->id,
            loanableType: LoanableType::fromModel($item),
            studentId: $data->studentId,
            professionalId: $data->professionalId,
        )) {
            throw new InvalidWaitlist('Este beneficiário já possui um empréstimo ativo deste recurso.');
        }
    }
}
