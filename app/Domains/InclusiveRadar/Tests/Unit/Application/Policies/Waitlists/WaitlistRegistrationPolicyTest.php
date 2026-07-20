<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Unit\Application\Policies\Waitlists;

use App\Domains\InclusiveRadar\Application\Data\Waitlists\CreateWaitlistData;
use App\Domains\InclusiveRadar\Application\Policies\Waitlists\WaitlistRegistrationPolicy;
use App\Domains\InclusiveRadar\Application\Queries\Loans\OpenLoanExistsQuery;
use App\Domains\InclusiveRadar\Application\Queries\Waitlists\ActiveWaitlistExistsQuery;
use App\Domains\InclusiveRadar\Domain\Enums\LoanableType;
use App\Domains\InclusiveRadar\Domain\Enums\ResourceStatus;
use App\Domains\InclusiveRadar\Domain\Exceptions\InvalidWaitlist;
use App\Domains\InclusiveRadar\Domain\Models\AssistiveTechnology;
use Tests\TestCase;

final class WaitlistRegistrationPolicyTest extends TestCase
{
    private WaitlistRegistrationPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();

        $this->policy = new WaitlistRegistrationPolicy(
            activeWaitlistExists: new ActiveWaitlistExistsQuery,
            openLoanExists: new OpenLoanExistsQuery,
        );
    }

    public function test_it_rejects_missing_beneficiary(): void
    {
        $this->expectException(InvalidWaitlist::class);
        $this->expectExceptionMessage('É necessário informar um aluno ou um profissional.');

        $this->policy->ensureCanRegister(
            item: $this->item(),
            data: $this->data(),
        );
    }

    public function test_it_rejects_two_beneficiaries(): void
    {
        $this->expectException(InvalidWaitlist::class);
        $this->expectExceptionMessage('Não é permitido informar aluno e profissional ao mesmo tempo.');

        $this->policy->ensureCanRegister(
            item: $this->item(),
            data: $this->data(studentId: 20, professionalId: 30),
        );
    }

    public function test_it_rejects_available_item(): void
    {
        $this->expectException(InvalidWaitlist::class);
        $this->expectExceptionMessage(
            'Este recurso ainda possui unidades disponíveis e pode ser emprestado, portanto não é possível criar uma fila de espera.'
        );

        $this->policy->ensureCanRegister(
            item: $this->item(quantityAvailable: 1),
            data: $this->data(studentId: 20),
        );
    }

    private function item(
        int $quantityAvailable = 0,
        ResourceStatus $status = ResourceStatus::AVAILABLE,
    ): AssistiveTechnology {
        return new AssistiveTechnology([
            'id' => 10,
            'quantity_available' => $quantityAvailable,
            'status' => $status,
        ]);
    }

    private function data(
        ?int $studentId = null,
        ?int $professionalId = null,
    ): CreateWaitlistData {
        return new CreateWaitlistData(
            waitlistableId: 10,
            waitlistableType: LoanableType::AssistiveTechnology,
            studentId: $studentId,
            professionalId: $professionalId,
        );
    }
}
