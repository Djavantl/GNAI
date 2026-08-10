<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Unit\Domain\Models;

use App\Domains\InclusiveRadar\Domain\DTOs\Barriers\CreateBarrierDTO;
use App\Domains\InclusiveRadar\Domain\DTOs\Barriers\UpdateBarrierDTO;
use App\Domains\InclusiveRadar\Domain\Enums\BarrierStatus;
use App\Domains\InclusiveRadar\Domain\Enums\Priority;
use App\Domains\InclusiveRadar\Domain\Exceptions\InvalidBarrier;
use App\Domains\InclusiveRadar\Domain\Models\Barrier;
use Tests\TestCase;

final class BarrierTest extends TestCase
{
    public function test_it_registers_an_anonymous_barrier(): void
    {
        $barrier = Barrier::register(new CreateBarrierDTO(
            name: ' Rampa bloqueada ',
            institutionId: 1,
            barrierCategoryId: 2,
            priority: Priority::HIGH,
            identifiedAt: '2026-07-22',
            registeredBy: 10,
            description: ' Acesso impedido. ',
            affectedStudentId: 99,
            isAnonymous: true,
            status: BarrierStatus::IDENTIFIED,
        ));

        self::assertSame('Rampa bloqueada', $barrier->name);
        self::assertSame('Acesso impedido.', $barrier->description);
        self::assertSame(10, $barrier->registered_by_user_id);
        self::assertTrue($barrier->is_anonymous);
        self::assertNull($barrier->affected_student_id);
        self::assertNull($barrier->affected_professional_id);
    }

    public function test_it_revises_general_report_context(): void
    {
        $barrier = new Barrier([
            'name' => 'Antiga',
            'institution_id' => 1,
            'barrier_category_id' => 2,
            'priority' => Priority::MEDIUM,
            'identified_at' => '2026-07-22',
        ]);

        $barrier->revise(new UpdateBarrierDTO(
            name: 'Relato geral',
            institutionId: 1,
            barrierCategoryId: 2,
            priority: Priority::LOW,
            identifiedAt: '2026-07-22',
            affectedStudentId: 10,
            affectedProfessionalId: 20,
            affectedPersonName: ' Visitante ',
            affectedPersonRole: ' Comunidade ',
            notApplicable: true,
        ));

        self::assertTrue($barrier->not_applicable);
        self::assertFalse($barrier->is_anonymous);
        self::assertNull($barrier->affected_student_id);
        self::assertNull($barrier->affected_professional_id);
        self::assertSame('Visitante', $barrier->affected_person_name);
        self::assertSame('Comunidade', $barrier->affected_person_role);
    }

    public function test_it_marks_resolution_when_status_requires_it(): void
    {
        $barrier = new Barrier;

        $barrier->resolveIfStatusRequires(BarrierStatus::RESOLVED);

        self::assertNotNull($barrier->resolved_at);
    }

    public function test_it_rejects_invalid_latitude(): void
    {
        $this->expectException(InvalidBarrier::class);
        $this->expectExceptionMessage('A latitude deve estar entre -90 e 90.');

        Barrier::register(new CreateBarrierDTO(
            name: 'Barreira',
            institutionId: 1,
            barrierCategoryId: 2,
            priority: Priority::HIGH,
            identifiedAt: '2026-07-22',
            registeredBy: 10,
            latitude: -100,
        ));
    }

    public function test_it_uses_barrier_morph_class(): void
    {
        self::assertSame('barrier', (new Barrier)->getMorphClass());
    }
}
