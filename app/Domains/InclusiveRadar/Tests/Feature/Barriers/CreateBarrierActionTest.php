<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Feature\Barriers;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\InclusiveRadar\Application\Actions\Barriers\CreateBarrierAction;
use App\Domains\InclusiveRadar\Application\Data\Barriers\CreateBarrierData;
use App\Domains\InclusiveRadar\Application\Data\Inspections\CreateInspectionData;
use App\Domains\InclusiveRadar\Domain\Enums\BarrierStatus;
use App\Domains\InclusiveRadar\Domain\Enums\InspectionType;
use App\Domains\InclusiveRadar\Domain\Models\BarrierCategory;
use App\Domains\InclusiveRadar\Domain\Models\Institution;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Deficiency;
use App\Enums\Priority;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class CreateBarrierActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_barrier_with_initial_inspection(): void
    {
        $user = User::factory()->create();
        $institution = Institution::factory()->create();
        $category = BarrierCategory::factory()->create();
        $deficiency = Deficiency::factory()->create();

        $barrier = app(CreateBarrierAction::class)->execute(
            new CreateBarrierData(
                name: 'Escada sem corrimão',
                institutionId: $institution->id,
                barrierCategoryId: $category->id,
                priority: Priority::HIGH,
                identifiedAt: now()->toDateString(),
                deficiencies: [$deficiency->id],
                inspection: new CreateInspectionData(
                    date: now()->toDateString(),
                    type: InspectionType::INITIAL,
                ),
                isAnonymous: true,
                status: BarrierStatus::IDENTIFIED,
            ),
            registeredBy: $user->id,
        );

        $this->assertDatabaseHas('barriers', [
            'id' => $barrier->id,
            'name' => 'Escada sem corrimão',
            'registered_by_user_id' => $user->id,
            'is_anonymous' => true,
        ]);
        $this->assertDatabaseHas('barrier_deficiency', [
            'barrier_id' => $barrier->id,
            'deficiency_id' => $deficiency->id,
        ]);
        $this->assertDatabaseHas('inspections', [
            'inspectable_id' => $barrier->id,
            'inspectable_type' => 'barrier',
            'status' => BarrierStatus::IDENTIFIED->value,
            'type' => InspectionType::INITIAL->value,
        ]);
    }
}
