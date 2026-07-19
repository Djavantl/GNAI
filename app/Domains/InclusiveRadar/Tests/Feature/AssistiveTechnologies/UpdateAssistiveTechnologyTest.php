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
use App\Enums\InclusiveRadar\LoanStatus;
use App\Models\InclusiveRadar\Loan;
use App\Models\SpecializedEducationalSupport\Deficiency;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

final class UpdateAssistiveTechnologyTest extends TestCase
{
    use RefreshDatabase;

    private const ENDPOINT = '/_tests/inclusive-radar/assistive-technologies';

    protected function setUp(): void
    {
        parent::setUp();

        Route::put(
            self::ENDPOINT.'/{assistiveTechnology}',
            [AssistiveTechnologyController::class, 'update'],
        )->middleware('web');
    }

    public function test_it_updates_the_technology_stock_audience_and_inspection(): void
    {
        $user = User::factory()->create();
        $oldAudience = Deficiency::factory()->create();
        $newAudience = Deficiency::factory()->create();
        $technology = $this->physicalTechnology();
        $technology->assignTargetAudience([$oldAudience->id]);

        $response = $this->actingAs($user)->put(
            self::ENDPOINT.'/'.$technology->id,
            $this->payload([
                'name' => 'Linha Braille atualizada',
                'quantity' => 5,
                'conservation_state' => ConservationState::REGULAR->value,
                'deficiencies' => [$newAudience->id],
                'inspection' => [
                    'date' => now()->toDateString(),
                    'type' => InspectionType::PERIODIC->value,
                    'description' => 'Desgaste identificado na revisão.',
                ],
            ]),
        );

        $response->assertRedirect(
            route('inclusive-radar.assistive-technologies.index'),
        );
        $response->assertSessionHas(
            'success',
            'Tecnologia assistiva atualizada com sucesso!',
        );

        $this->assertDatabaseHas('assistive_technologies', [
            'id' => $technology->id,
            'name' => 'Linha Braille atualizada',
            'quantity' => 5,
            'quantity_available' => 5,
            'conservation_state' => ConservationState::REGULAR->value,
        ]);
        $this->assertDatabaseMissing('assistive_technology_deficiency', [
            'assistive_technology_id' => $technology->id,
            'deficiency_id' => $oldAudience->id,
        ]);
        $this->assertDatabaseHas('assistive_technology_deficiency', [
            'assistive_technology_id' => $technology->id,
            'deficiency_id' => $newAudience->id,
        ]);
        $this->assertDatabaseHas('inspections', [
            'inspectable_id' => $technology->id,
            'inspectable_type' => 'assistive_technology',
            'state' => ConservationState::REGULAR->value,
            'type' => InspectionType::PERIODIC->value,
            'description' => 'Desgaste identificado na revisão.',
            'user_id' => $user->id,
        ]);
    }

    public function test_it_preserves_open_loans_when_recalculating_available_stock(): void
    {
        $user = User::factory()->create();
        $audience = Deficiency::factory()->create();
        $technology = $this->physicalTechnology();
        $this->createOpenLoan($technology, $user);

        $response = $this->actingAs($user)->put(
            self::ENDPOINT.'/'.$technology->id,
            $this->payload([
                'quantity' => 4,
                'deficiencies' => [$audience->id],
            ]),
        );

        $response->assertRedirect(
            route('inclusive-radar.assistive-technologies.index'),
        );
        $this->assertDatabaseHas('assistive_technologies', [
            'id' => $technology->id,
            'quantity' => 4,
            'quantity_available' => 3,
        ]);
    }

    public function test_it_rejects_stock_lower_than_the_number_of_open_loans(): void
    {
        $user = User::factory()->create();
        $audience = Deficiency::factory()->create();
        $technology = $this->physicalTechnology();
        $this->createOpenLoan($technology, $user);
        $this->createOpenLoan($technology, $user);

        $response = $this->actingAs($user)
            ->from('/assistive-technologies/'.$technology->id.'/edit')
            ->put(
                self::ENDPOINT.'/'.$technology->id,
                $this->payload([
                    'quantity' => 1,
                    'deficiencies' => [$audience->id],
                ]),
            );

        $response->assertRedirect(
            '/assistive-technologies/'.$technology->id.'/edit',
        );
        $response->assertSessionHas(
            'error',
            'Impossível reduzir estoque: existem 2 unidades emprestadas.',
        );
        $this->assertDatabaseHas('assistive_technologies', [
            'id' => $technology->id,
            'quantity' => 2,
            'quantity_available' => 2,
        ]);
    }

    public function test_it_does_not_create_an_inspection_without_a_relevant_change(): void
    {
        $user = User::factory()->create();
        $audience = Deficiency::factory()->create();
        $technology = $this->physicalTechnology();

        $response = $this->actingAs($user)->put(
            self::ENDPOINT.'/'.$technology->id,
            $this->payload([
                'name' => 'Apenas novo nome',
                'deficiencies' => [$audience->id],
            ]),
        );

        $response->assertRedirect(
            route('inclusive-radar.assistive-technologies.index'),
        );
        $this->assertDatabaseCount('inspections', 0);
    }

    private function physicalTechnology(): AssistiveTechnology
    {
        $technology = AssistiveTechnology::register(new CreateAssistiveTechnologyDTO(
            name: 'Linha Braille',
            digital: false,
            loanable: true,
            quantity: 2,
            assetCode: AssetCode::from('TA-1001'),
            conservationState: ConservationState::GOOD,
        ));
        $technology->save();

        return $technology;
    }

    private function createOpenLoan(
        AssistiveTechnology $technology,
        User $user,
    ): void {
        Loan::query()->create([
            'loanable_id' => $technology->id,
            'loanable_type' => $technology->getMorphClass(),
            'user_id' => $user->id,
            'loan_date' => now(),
            'due_date' => now()->addWeek(),
            'return_date' => null,
            'status' => LoanStatus::ACTIVE,
        ]);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return array_replace_recursive([
            'name' => 'Linha Braille',
            'is_digital' => false,
            'is_loanable' => true,
            'quantity' => 2,
            'asset_code' => 'TA-1001',
            'conservation_state' => ConservationState::GOOD->value,
            'status' => ResourceStatus::AVAILABLE->value,
            'is_active' => true,
            'deficiencies' => [],
            'inspection' => [
                'date' => now()->toDateString(),
                'type' => InspectionType::PERIODIC->value,
                'description' => null,
                'images' => [],
            ],
        ], $overrides);
    }
}
