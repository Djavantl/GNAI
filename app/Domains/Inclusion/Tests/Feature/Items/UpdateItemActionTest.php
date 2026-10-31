<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Tests\Feature\Items;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\Inclusion\Application\Actions\Items\UpdateItemAction;
use App\Domains\Inclusion\Application\Data\Inspections\CreateInspectionData;
use App\Domains\Inclusion\Application\Data\Items\UpdateItemData;
use App\Domains\Inclusion\Domain\Enums\ConservationState;
use App\Domains\Inclusion\Domain\Enums\InspectionType;
use App\Domains\Inclusion\Domain\Enums\ItemType;
use App\Domains\Inclusion\Domain\Enums\ResourceStatus;
use App\Domains\Inclusion\Domain\Exceptions\AssetCodeAlreadyInUse;
use App\Domains\Inclusion\Domain\Exceptions\InvalidItem;
use App\Domains\Inclusion\Domain\Exceptions\InvalidStock;
use App\Domains\Inclusion\Domain\Models\AccessibilityFeature;
use App\Domains\Inclusion\Domain\Models\Deficiency;
use App\Domains\Inclusion\Domain\Models\Item;
use App\Domains\Inclusion\Domain\Models\Loan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class UpdateItemActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_updates_the_item_replacing_relations_and_registering_an_inspection_on_state_change(): void
    {
        $user = User::factory()->create();
        $newAudience = Deficiency::factory()->create();
        $newFeature = AccessibilityFeature::factory()->create();
        $item = Item::factory()
            ->physical()
            ->withDeficiencies()
            ->withAccessibilityFeatures()
            ->create([
                'quantity' => 2,
                'quantity_available' => 2,
                'conservation_state' => ConservationState::GOOD,
            ]);

        $updated = app(UpdateItemAction::class)->execute(
            item: $item,
            data: $this->data(
                deficiencies: [$newAudience->id],
                quantity: 5,
                conservationState: ConservationState::REGULAR,
                accessibilityFeatures: [$newFeature->id],
            ),
            registeredBy: $user->id,
        );

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'name' => 'Linha Braille atualizada',
            'quantity' => 5,
            'quantity_available' => 5,
            'conservation_state' => ConservationState::REGULAR->value,
        ]);
        self::assertSame([$newAudience->id], $updated->deficiencies->modelKeys());
        self::assertSame([$newFeature->id], $updated->accessibilityFeatures->modelKeys());
        $this->assertDatabaseHas('inspections', [
            'inspectable_id' => $item->id,
            'inspectable_type' => 'item',
            'state' => ConservationState::REGULAR->value,
            'type' => InspectionType::PERIODIC->value,
            'description' => 'Vistoria periódica de atualização.',
            'user_id' => $user->id,
        ]);
    }

    public function test_it_does_not_register_an_inspection_without_a_relevant_change(): void
    {
        $deficiency = Deficiency::factory()->create();
        $item = Item::factory()->physical()->create([
            'quantity' => 2,
            'quantity_available' => 2,
            'conservation_state' => ConservationState::GOOD,
        ]);

        app(UpdateItemAction::class)->execute(
            item: $item,
            data: $this->data(deficiencies: [$deficiency->id]),
            registeredBy: User::factory()->create()->id,
        );

        $this->assertDatabaseCount('inspections', 0);
    }

    public function test_it_preserves_open_loans_when_recalculating_available_stock(): void
    {
        $deficiency = Deficiency::factory()->create();
        $item = Item::factory()->physical()->create(['quantity' => 2, 'quantity_available' => 1]);
        Loan::factory()->forAssistiveTechnology($item)->create();
        Loan::factory()->forAssistiveTechnology($item)->returned()->create();

        app(UpdateItemAction::class)->execute(
            item: $item,
            data: $this->data(deficiencies: [$deficiency->id], quantity: 4),
            registeredBy: User::factory()->create()->id,
        );

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'quantity' => 4,
            'quantity_available' => 3,
        ]);
    }

    public function test_it_rejects_stock_lower_than_the_number_of_open_loans(): void
    {
        $deficiency = Deficiency::factory()->create();
        $item = Item::factory()->physical()->create(['quantity' => 2, 'quantity_available' => 0]);
        Loan::factory()->count(2)->forAssistiveTechnology($item)->create();

        $this->expectException(InvalidStock::class);
        $this->expectExceptionMessage('Impossível reduzir estoque: existem 2 unidades emprestadas.');

        app(UpdateItemAction::class)->execute(
            item: $item,
            data: $this->data(deficiencies: [$deficiency->id], quantity: 1),
            registeredBy: User::factory()->create()->id,
        );
    }

    public function test_it_rejects_a_status_change_while_there_are_open_loans(): void
    {
        $deficiency = Deficiency::factory()->create();
        $item = Item::factory()->physical()->create([
            'quantity' => 2,
            'quantity_available' => 1,
            'status' => ResourceStatus::AVAILABLE,
        ]);
        Loan::factory()->forAssistiveTechnology($item)->create();

        try {
            app(UpdateItemAction::class)->execute(
                item: $item,
                data: $this->data(
                    deficiencies: [$deficiency->id],
                    status: ResourceStatus::UNDER_MAINTENANCE,
                ),
                registeredBy: User::factory()->create()->id,
            );
            self::fail('Era esperado InvalidItem.');
        } catch (InvalidItem) {
            $this->assertDatabaseHas('items', [
                'id' => $item->id,
                'status' => ResourceStatus::AVAILABLE->value,
            ]);
        }
    }

    public function test_it_clears_the_stock_when_the_item_becomes_digital(): void
    {
        $deficiency = Deficiency::factory()->create();
        $item = Item::factory()->physical()->create(['quantity' => 3, 'quantity_available' => 3]);

        app(UpdateItemAction::class)->execute(
            item: $item,
            data: $this->data(
                deficiencies: [$deficiency->id],
                isDigital: true,
                quantity: 3,
            ),
            registeredBy: User::factory()->create()->id,
        );

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'is_digital' => true,
            'quantity' => null,
            'quantity_available' => null,
        ]);
    }

    public function test_it_keeps_its_own_asset_code_but_rejects_one_used_by_another_item(): void
    {
        $deficiency = Deficiency::factory()->create();
        Item::factory()->physical()->create(['asset_code' => 'TA-2002']);
        $item = Item::factory()->physical()->create([
            'asset_code' => 'TA-2001',
            'quantity' => 2,
            'quantity_available' => 2,
        ]);

        app(UpdateItemAction::class)->execute(
            item: $item,
            data: $this->data(deficiencies: [$deficiency->id], assetCode: 'TA-2001'),
            registeredBy: User::factory()->create()->id,
        );

        $this->expectException(AssetCodeAlreadyInUse::class);

        app(UpdateItemAction::class)->execute(
            item: $item,
            data: $this->data(deficiencies: [$deficiency->id], assetCode: 'TA-2002'),
            registeredBy: User::factory()->create()->id,
        );
    }

    /**
     * @param  list<int>  $deficiencies
     * @param  list<int>  $accessibilityFeatures
     */
    private function data(
        array $deficiencies,
        ?int $quantity = 2,
        bool $isDigital = false,
        ?string $assetCode = null,
        ConservationState $conservationState = ConservationState::GOOD,
        ResourceStatus $status = ResourceStatus::AVAILABLE,
        array $accessibilityFeatures = [],
    ): UpdateItemData {
        return new UpdateItemData(
            itemType: ItemType::AssistiveTechnology,
            name: 'Linha Braille atualizada',
            isDigital: $isDigital,
            isLoanable: true,
            quantity: $quantity,
            assetCode: $assetCode,
            conservationState: $conservationState,
            status: $status,
            isActive: true,
            deficiencies: $deficiencies,
            inspection: new CreateInspectionData(
                date: now()->toDateString(),
                type: InspectionType::PERIODIC,
            ),
            accessibilityFeatures: $accessibilityFeatures,
        );
    }
}
