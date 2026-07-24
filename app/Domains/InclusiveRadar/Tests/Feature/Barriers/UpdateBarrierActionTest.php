<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Feature\Barriers;

use App\Domains\InclusiveRadar\Application\Actions\Barriers\UpdateBarrierAction;
use App\Domains\InclusiveRadar\Application\Data\Barriers\UpdateBarrierData;
use App\Domains\InclusiveRadar\Application\Data\Inspections\CreateInspectionData;
use App\Domains\InclusiveRadar\Domain\Enums\BarrierStatus;
use App\Domains\InclusiveRadar\Domain\Enums\InspectionType;
use App\Domains\InclusiveRadar\Domain\Models\Barrier;
use App\Domains\InclusiveRadar\Domain\Models\Inspection;
use App\Enums\Priority;
use App\Models\SpecializedEducationalSupport\Deficiency;
use App\Domains\Auth\Domain\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class UpdateBarrierActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_updates_a_barrier_to_resolved_and_registers_inspection(): void
    {
        $user = User::factory()->create();
        $deficiency = Deficiency::factory()->create();
        $barrier = Barrier::factory()->anonymous()->create([
            'resolved_at' => null,
            'priority' => Priority::HIGH,
        ]);
        $barrier->deficiencies()->sync([$deficiency->id]);
        Inspection::factory()->forBarrier($barrier)->create([
            'status' => BarrierStatus::IDENTIFIED->value,
            'type' => InspectionType::INITIAL,
        ]);

        $updated = app(UpdateBarrierAction::class)->execute(
            $barrier,
            new UpdateBarrierData(
                name: $barrier->name,
                institutionId: $barrier->institution_id,
                barrierCategoryId: $barrier->barrier_category_id,
                priority: Priority::HIGH,
                identifiedAt: $barrier->identified_at->toDateString(),
                deficiencies: [$deficiency->id],
                inspection: new CreateInspectionData(
                    date: now()->toDateString(),
                    type: InspectionType::PERIODIC,
                    description: 'Barreira resolvida.',
                ),
                isAnonymous: true,
                status: BarrierStatus::RESOLVED,
            ),
            registeredBy: $user->id,
        );

        self::assertNotNull($updated->resolved_at);
        $this->assertDatabaseHas('inspections', [
            'inspectable_id' => $barrier->id,
            'inspectable_type' => 'barrier',
            'status' => BarrierStatus::RESOLVED->value,
            'type' => InspectionType::PERIODIC->value,
        ]);
    }

    public function test_it_does_not_register_inspection_without_status_change_or_interaction(): void
    {
        $user = User::factory()->create();
        $deficiency = Deficiency::factory()->create();
        $barrier = Barrier::factory()->anonymous()->create([
            'priority' => Priority::HIGH,
        ]);
        $barrier->deficiencies()->sync([$deficiency->id]);
        Inspection::factory()->forBarrier($barrier)->create([
            'status' => BarrierStatus::IDENTIFIED->value,
            'type' => InspectionType::INITIAL,
        ]);
        $initialCount = $barrier->inspections()->count();

        app(UpdateBarrierAction::class)->execute(
            $barrier,
            new UpdateBarrierData(
                name: $barrier->name,
                institutionId: $barrier->institution_id,
                barrierCategoryId: $barrier->barrier_category_id,
                priority: Priority::HIGH,
                identifiedAt: $barrier->identified_at->toDateString(),
                deficiencies: [$deficiency->id],
                inspection: new CreateInspectionData(
                    date: now()->toDateString(),
                    type: InspectionType::PERIODIC,
                ),
                isAnonymous: true,
            ),
            registeredBy: $user->id,
        );

        self::assertSame($initialCount, $barrier->fresh()->inspections()->count());
    }
}
