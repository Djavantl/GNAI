<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Feature\AssistiveTechnologies;

use App\Domains\InclusiveRadar\Domain\DTOs\AssistiveTechnologies\CreateAssistiveTechnologyDTO;
use App\Domains\InclusiveRadar\Domain\Enums\ConservationState;
use App\Domains\InclusiveRadar\Domain\Enums\InspectionType;
use App\Domains\InclusiveRadar\Domain\Enums\ResourceStatus;
use App\Domains\InclusiveRadar\Domain\Models\AssistiveTechnology;
use App\Domains\InclusiveRadar\Domain\ValueObjects\AssetCode;
use App\Domains\InclusiveRadar\UI\Controllers\AssistiveTechnologyController;
use App\Models\SpecializedEducationalSupport\Deficiency;
use App\Domains\Auth\Domain\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

final class CreateAssistiveTechnologyTest extends TestCase
{
    use RefreshDatabase;

    private const ENDPOINT = '/_tests/inclusive-radar/assistive-technologies';

    protected function setUp(): void
    {
        parent::setUp();

        Route::post(
            self::ENDPOINT,
            [AssistiveTechnologyController::class, 'store'],
        )->middleware('web');
    }

    public function test_it_creates_a_physical_assistive_technology(): void
    {
        $user = User::factory()->create();
        $deficiencies = Deficiency::factory()->count(2)->create();

        $response = $this->actingAs($user)->post(self::ENDPOINT, [
            'name' => 'Linha Braille Portátil',
            'is_digital' => false,
            'is_loanable' => true,
            'quantity' => 3,
            'asset_code' => 'TA-1001',
            'conservation_state' => ConservationState::GOOD->value,
            'deficiencies' => $deficiencies->modelKeys(),
            'inspection' => [
                'date' => now()->toDateString(),
                'type' => InspectionType::INITIAL->value,
                'description' => 'Cadastro inicial',
                'evidences' => [],
            ],
            'status' => ResourceStatus::AVAILABLE->value,
            'is_active' => true,
        ]);

        $response->assertRedirect(
            route('inclusive-radar.assistive-technologies.index'),
        );
        $response->assertSessionHas(
            'success',
            'Tecnologia assistiva criada com sucesso!',
        );

        $this->assertDatabaseHas('assistive_technologies', [
            'name' => 'Linha Braille Portátil',
            'asset_code' => 'TA-1001',
            'quantity' => 3,
            'quantity_available' => 3,
            'is_digital' => false,
            'is_loanable' => true,
        ]);

        $technologyId = (int) DB::table('assistive_technologies')
            ->where('asset_code', 'TA-1001')
            ->value('id');

        foreach ($deficiencies as $deficiency) {
            $this->assertDatabaseHas('assistive_technology_deficiency', [
                'assistive_technology_id' => $technologyId,
                'deficiency_id' => $deficiency->id,
            ]);

            $targetAudience = DB::table('assistive_technology_deficiency')
                ->where('assistive_technology_id', $technologyId)
                ->where('deficiency_id', $deficiency->id)
                ->first();

            self::assertNotNull($targetAudience?->created_at);
            self::assertNotNull($targetAudience?->updated_at);
        }

        $this->assertDatabaseHas('inspections', [
            'inspectable_id' => $technologyId,
            'inspectable_type' => 'assistive_technology',
            'state' => ConservationState::GOOD->value,
            'status' => null,
            'type' => InspectionType::INITIAL->value,
            'description' => 'Cadastro inicial',
            'user_id' => $user->id,
        ]);
    }

    public function test_it_creates_a_loanable_digital_technology_without_stock(): void
    {
        $user = User::factory()->create();
        $deficiency = Deficiency::factory()->create();

        $response = $this->actingAs($user)->post(self::ENDPOINT, [
            'name' => 'Leitor de tela',
            'is_digital' => true,
            'is_loanable' => true,
            'quantity' => null,
            'asset_code' => null,
            'conservation_state' => ConservationState::NOT_APPLICABLE->value,
            'deficiencies' => [$deficiency->id],
            'inspection' => [
                'date' => now()->toDateString(),
                'type' => InspectionType::INITIAL->value,
            ],
            'status' => ResourceStatus::AVAILABLE->value,
            'is_active' => true,
        ]);

        $response->assertRedirect(
            route('inclusive-radar.assistive-technologies.index'),
        );

        $this->assertDatabaseHas('assistive_technologies', [
            'name' => 'Leitor de tela',
            'is_digital' => true,
            'is_loanable' => true,
            'quantity' => null,
            'quantity_available' => null,
        ]);
    }

    public function test_it_rejects_a_physical_technology_without_quantity(): void
    {
        $user = User::factory()->create();
        $deficiency = Deficiency::factory()->create();

        $response = $this->actingAs($user)
            ->from('/assistive-technologies/create')
            ->post(self::ENDPOINT, [
                'name' => 'Mouse adaptado',
                'is_digital' => false,
                'is_loanable' => true,
                'quantity' => null,
                'asset_code' => null,
                'conservation_state' => ConservationState::GOOD->value,
                'deficiencies' => [$deficiency->id],
                'inspection' => [
                    'date' => now()->toDateString(),
                    'type' => InspectionType::INITIAL->value,
                ],
                'status' => ResourceStatus::AVAILABLE->value,
                'is_active' => true,
            ]);

        $response->assertRedirect('/assistive-technologies/create');
        $response->assertSessionHasErrors('quantity');
        $this->assertDatabaseMissing('assistive_technologies', [
            'name' => 'Mouse adaptado',
        ]);
    }

    public function test_it_rejects_creation_without_a_target_audience(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->from('/assistive-technologies/create')
            ->post(self::ENDPOINT, [
                'name' => 'Teclado adaptado',
                'is_digital' => false,
                'is_loanable' => true,
                'quantity' => 1,
                'asset_code' => null,
                'conservation_state' => ConservationState::GOOD->value,
                'deficiencies' => [],
                'inspection' => [
                    'date' => now()->toDateString(),
                    'type' => InspectionType::INITIAL->value,
                ],
                'status' => ResourceStatus::AVAILABLE->value,
                'is_active' => true,
            ]);

        $response->assertRedirect('/assistive-technologies/create');
        $response->assertSessionHasErrors('deficiencies');
        $this->assertDatabaseMissing('assistive_technologies', [
            'name' => 'Teclado adaptado',
        ]);
    }

    public function test_it_rejects_a_normalized_asset_code_already_in_use(): void
    {
        $existingTechnology = AssistiveTechnology::register(new CreateAssistiveTechnologyDTO(
            name: 'Linha Braille existente',
            isDigital: false,
            isLoanable: true,
            quantity: 1,
            assetCode: AssetCode::from('TA-2001'),
            conservationState: ConservationState::GOOD,
        ));
        $existingTechnology->save();

        $user = User::factory()->create();
        $deficiency = Deficiency::factory()->create();

        $response = $this->actingAs($user)
            ->from('/assistive-technologies/create')
            ->post(self::ENDPOINT, [
                'name' => 'Linha Braille duplicada',
                'is_digital' => false,
                'is_loanable' => true,
                'quantity' => 1,
                'asset_code' => ' TA-2001 ',
                'conservation_state' => ConservationState::GOOD->value,
                'deficiencies' => [$deficiency->id],
                'inspection' => [
                    'date' => now()->toDateString(),
                    'type' => InspectionType::INITIAL->value,
                ],
                'status' => ResourceStatus::AVAILABLE->value,
                'is_active' => true,
            ]);

        $response->assertRedirect('/assistive-technologies/create');
        $response->assertSessionHasErrors([
            'asset_code' => 'O código patrimonial já está em uso.',
        ]);
        $this->assertDatabaseCount('assistive_technologies', 1);
    }
}
