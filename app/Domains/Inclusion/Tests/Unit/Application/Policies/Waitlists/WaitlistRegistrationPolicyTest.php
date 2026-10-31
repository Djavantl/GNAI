<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Tests\Unit\Application\Policies\Waitlists;

use App\Domains\Inclusion\Application\Data\Waitlists\CreateWaitlistData;
use App\Domains\Inclusion\Application\Policies\Waitlists\WaitlistRegistrationPolicy;
use App\Domains\Inclusion\Domain\Enums\ItemType;
use App\Domains\Inclusion\Domain\Exceptions\InvalidWaitlist;
use App\Domains\Inclusion\Domain\Models\Item;
use Tests\TestCase;

final class WaitlistRegistrationPolicyTest extends TestCase
{
    public function test_it_rejects_missing_beneficiary(): void
    {
        $this->expectException(InvalidWaitlist::class);
        $this->expectExceptionMessage('É necessário informar um aluno ou um profissional.');

        app(WaitlistRegistrationPolicy::class)->ensureCanRegister(
            item: new Item,
            data: $this->data(),
        );
    }

    public function test_it_rejects_two_beneficiaries(): void
    {
        $this->expectException(InvalidWaitlist::class);
        $this->expectExceptionMessage('Não é permitido informar aluno e profissional ao mesmo tempo.');

        app(WaitlistRegistrationPolicy::class)->ensureCanRegister(
            item: new Item,
            data: $this->data(studentId: 20, professionalId: 30),
        );
    }

    private function data(?int $studentId = null, ?int $professionalId = null): CreateWaitlistData
    {
        return new CreateWaitlistData(
            waitlistableId: 10,
            itemType: ItemType::AssistiveTechnology,
            studentId: $studentId,
            professionalId: $professionalId,
        );
    }
}
