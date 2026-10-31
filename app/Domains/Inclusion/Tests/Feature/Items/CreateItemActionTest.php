<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Tests\Feature\Items;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\Inclusion\Application\Actions\Items\CreateItemAction;
use App\Domains\Inclusion\Application\Data\Inspections\CreateInspectionData;
use App\Domains\Inclusion\Application\Data\Items\CreateItemData;
use App\Domains\Inclusion\Domain\Enums\ConservationState;
use App\Domains\Inclusion\Domain\Enums\InspectionType;
use App\Domains\Inclusion\Domain\Enums\ItemType;
use App\Domains\Inclusion\Domain\Exceptions\AssetCodeAlreadyInUse;
use App\Domains\Inclusion\Domain\Exceptions\InvalidItem;
use App\Domains\Inclusion\Domain\Exceptions\InvalidStock;
use App\Domains\Inclusion\Domain\Models\AccessibilityFeature;
use App\Domains\Inclusion\Domain\Models\Deficiency;
use App\Domains\Inclusion\Domain\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class CreateItemActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_physical_item_with_full_stock_audience_and_initial_inspection(): void
    {
        $user = User::factory()->create();
        $deficiencies = Deficiency::factory()->count(2)->create();
        $feature = AccessibilityFeature::factory()->create();

        $item = app(CreateItemAction::class)->execute(
            data: new CreateItemData(
                itemType: ItemType::AssistiveTechnology,
                name: ' Linha Braille ',
                isDigital: false,
                isLoanable: true,
                quantity: 3,
                assetCode: ' TA-1001 ',
                conservationState: ConservationState::GOOD,
                deficiencies: $deficiencies->modelKeys(),
                inspection: new CreateInspectionData(
                    date: now()->toDateString(),
                    description: 'Cadastro inicial',
                ),
                accessibilityFeatures: [$feature->id],
            ),
            registeredBy: $user->id,
        );

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'item_type' => ItemType::AssistiveTechnology->value,
            'name' => 'Linha Braille',
            'asset_code' => 'TA-1001',
            'quantity' => 3,
            'quantity_available' => 3,
            'is_digital' => false,
        ]);
        self::assertEqualsCanonicalizing(
            $deficiencies->modelKeys(),
            $item->deficiencies->modelKeys(),
        );
        self::assertSame([$feature->id], $item->accessibilityFeatures->modelKeys());
        $this->assertDatabaseHas('inspections', [
            'inspectable_id' => $item->id,
            'inspectable_type' => 'item',
            'state' => ConservationState::GOOD->value,
            'type' => InspectionType::INITIAL->value,
            'description' => 'Cadastro inicial',
            'user_id' => $user->id,
        ]);
    }

    public function test_it_creates_a_digital_item_without_stock(): void
    {
        $user = User::factory()->create();
        $deficiency = Deficiency::factory()->create();

        $item = app(CreateItemAction::class)->execute(
            data: new CreateItemData(
                itemType: ItemType::AccessibleEducationalMaterial,
                name: 'Apostila em áudio',
                isDigital: true,
                isLoanable: false,
                quantity: 5,
                assetCode: null,
                conservationState: ConservationState::NOT_APPLICABLE,
                deficiencies: [$deficiency->id],
                inspection: new CreateInspectionData(date: now()->toDateString()),
            ),
            registeredBy: $user->id,
        );

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'is_digital' => true,
            'quantity' => null,
            'quantity_available' => null,
            'asset_code' => null,
        ]);
        $this->assertDatabaseHas('inspections', [
            'inspectable_id' => $item->id,
            'description' => 'Vistoria inicial de entrada.',
        ]);
    }

    public function test_it_rejects_a_physical_item_without_quantity(): void
    {
        $deficiency = Deficiency::factory()->create();

        try {
            app(CreateItemAction::class)->execute(
                data: $this->physicalItemData(
                    deficiencies: [$deficiency->id],
                    quantity: null,
                ),
                registeredBy: User::factory()->create()->id,
            );
            self::fail('Era esperado InvalidStock.');
        } catch (InvalidStock) {
            $this->assertDatabaseCount('items', 0);
        }
    }

    public function test_it_rolls_back_the_item_when_no_target_audience_is_given(): void
    {
        try {
            app(CreateItemAction::class)->execute(
                data: $this->physicalItemData(deficiencies: []),
                registeredBy: User::factory()->create()->id,
            );
            self::fail('Era esperado InvalidItem.');
        } catch (InvalidItem $exception) {
            self::assertSame('Selecione pelo menos um público-alvo.', $exception->getMessage());
            $this->assertDatabaseCount('items', 0);
        }
    }

    public function test_it_rejects_an_asset_code_already_in_use(): void
    {
        Item::factory()->physical()->create(['asset_code' => 'TA-2001']);
        $deficiency = Deficiency::factory()->create();

        $this->expectException(AssetCodeAlreadyInUse::class);

        app(CreateItemAction::class)->execute(
            data: $this->physicalItemData(
                deficiencies: [$deficiency->id],
                assetCode: ' TA-2001 ',
            ),
            registeredBy: User::factory()->create()->id,
        );
    }

    public function test_it_rejects_the_asset_code_of_a_deleted_item(): void
    {
        Item::factory()->physical()->create(['asset_code' => 'TA-3001'])->delete();
        $deficiency = Deficiency::factory()->create();

        try {
            app(CreateItemAction::class)->execute(
                data: $this->physicalItemData(
                    deficiencies: [$deficiency->id],
                    assetCode: 'TA-3001',
                ),
                registeredBy: User::factory()->create()->id,
            );
            self::fail('Era esperado AssetCodeAlreadyInUse.');
        } catch (AssetCodeAlreadyInUse) {
            $this->assertDatabaseCount('items', 1);
        }
    }

    /**
     * @param  list<int>  $deficiencies
     */
    private function physicalItemData(
        array $deficiencies,
        ?int $quantity = 1,
        ?string $assetCode = null,
    ): CreateItemData {
        return new CreateItemData(
            itemType: ItemType::AssistiveTechnology,
            name: 'Teclado adaptado',
            isDigital: false,
            isLoanable: true,
            quantity: $quantity,
            assetCode: $assetCode,
            conservationState: ConservationState::GOOD,
            deficiencies: $deficiencies,
            inspection: new CreateInspectionData(date: now()->toDateString()),
        );
    }
}
